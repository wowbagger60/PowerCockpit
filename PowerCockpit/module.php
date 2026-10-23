<?php
declare(strict_types=1);
class PowerCockpit extends IPSModuleStrict
{
    public function Create(): void
    {
        parent::Create();
        $this->RegisterPropertyInteger('SchemaVersion', 0);
        $this->RegisterPropertyString('Panels', ''); // Empty string denotes an installation before Build 7.
        $this->RegisterPropertyInteger('PhaseCount', 3);
        $this->RegisterPropertyBoolean('ShowVoltage', true);
        $this->RegisterPropertyInteger('Timeout', 30);
        $this->RegisterPropertyInteger('PowerUnit', 1);
        $this->RegisterPropertyFloat('NominalVoltage', 220);
        // Keep all original properties for lossless migration of existing instances.
        $this->RegisterPropertyString('PanelName', '');
        $this->RegisterPropertyInteger('TileTarget', 1);
        $this->RegisterPropertyFloat('Fuse', 40);
        foreach ($this->Keys() as $key) {
            $this->RegisterPropertyInteger($key, 0);
            if (!str_starts_with($key, 'Current')) {
                $this->RegisterPropertyInteger($key.'Target', 1);
                foreach (['Min','Max','Avg'] as $s) $this->RegisterPropertyInteger($key.$s, 0);
            }
        }
        foreach ($this->Defaults() as $name=>$zones) {
            $this->RegisterPropertyFloat($name.'Start', $name === 'Voltage' ? 160 : 0);
            $this->RegisterPropertyString($name.'Zones', json_encode($zones));
        }
        $this->RegisterTimer('Refresh', 5000, 'PWRCP_Refresh($_IPS["TARGET"]);');
        $this->SetVisualizationType(1);
    }
    private function Phases(): array { return array_slice(['A','B','C'], 0, max(1,min(3,$this->ReadPropertyInteger('PhaseCount')))); }
    private function Keys(): array { return ['TotalPower','VoltageA','VoltageB','VoltageC','PowerA','PowerB','PowerC','CurrentA','CurrentB','CurrentC']; }
    private function Defaults(): array
    {
        return ['Voltage'=>[['End'=>198,'Color'=>0xE5AD22],['End'=>242,'Color'=>0x39A76B],['End'=>280,'Color'=>0xDB5149]],
            'Total'=>[['End'=>20000,'Color'=>0x39A76B],['End'=>26400,'Color'=>0xE5AD22],['End'=>30000,'Color'=>0xDB5149]],
            'Phase'=>[['End'=>6500,'Color'=>0x39A76B],['End'=>8800,'Color'=>0xE5AD22],['End'=>10000,'Color'=>0xDB5149]]];
    }
    private function PanelDefaults(): array
    {
        $p=['ID'=>'','Name'=>'','Target'=>1,'Fuse'=>40.0,'ShowTotal'=>true,'ShowPhase'=>true,'TotalBidirectional'=>false,'PhaseBidirectional'=>false,'TotalZones'=>$this->Defaults()['Total'],'PhaseZones'=>$this->Defaults()['Phase']];
        foreach (['TotalPower','PowerA','PowerB','PowerC','CurrentA','CurrentB','CurrentC'] as $key) {
            $p[$key]=0;
            if (!str_starts_with($key,'Current')) { $p[$key.'Target']=1; $p[$key.'Mode']=0; $p[$key.'Export']=0; $p[$key.'Invert']=false; foreach (['Min','Max','Avg'] as $s) $p[$key.$s]=0; }
        }
        return $p;
    }
    private function LegacyPanel(): array
    {
        $p=$this->PanelDefaults(); $p['ID']='legacy'; $p['Name']=$this->ReadPropertyString('PanelName');
        // Historically 0 meant no target in this module. Preserve that intent on migration.
        $p['Target']=max(1,$this->ReadPropertyInteger('TileTarget')); $p['Fuse']=$this->ReadPropertyFloat('Fuse');
        foreach ($p as $key=>$value) {
            if (preg_match('/^(TotalPower|Power[ABC]|Current[ABC])(Min|Max|Avg|Target)?$/D',$key)) $p[$key]=$this->ReadPropertyInteger($key);
        }
        foreach ($p as $key=>$value) if (str_ends_with($key,'Target')) $p[$key]=max(1,(int)$value);
        foreach (['Total','Phase'] as $name) $p[$name.'Zones']=json_decode($this->ReadPropertyString($name.'Zones'),true,512,JSON_THROW_ON_ERROR);
        return $p;
    }
    private function Panels(): array
    {
        if ($this->ReadPropertyString('Panels') === '') return [$this->LegacyPanel()];
        $rows=json_decode($this->ReadPropertyString('Panels'),true,512,JSON_THROW_ON_ERROR);
        if (!is_array($rows) || !array_is_list($rows)) throw new Exception('Ungültige Panelliste');
        foreach ($rows as &$row) {
            if (!is_array($row)) throw new Exception('Ungültiges Panel');
            $row=array_replace($this->PanelDefaults(),$row);
        }
        return $rows;
    }
    public function ApplyChanges(): void
    {
        parent::ApplyChanges();
        $this->SetVisualizationType(version_compare(IPS_GetKernelVersion(),'9.1','>=') ? 2 : 1);
        // Persist migration once; do not recreate panels after an intentional deletion.
        try {
            $panels=$this->Panels(); $changed=$this->ReadPropertyString('Panels')===''; $seen=[];
            if ($this->ReadPropertyInteger('SchemaVersion')<1) {
                foreach (['A','B','C'] as $phase) {
                    $key='Voltage'.$phase.'Target';
                    if ($this->ReadPropertyInteger($key)<=0) IPS_SetProperty($this->InstanceID,$key,1);
                }
                IPS_SetProperty($this->InstanceID,'SchemaVersion',1); $changed=true;
            }
            foreach ($panels as &$panel) {
                if ($panel['ID']==='' || isset($seen[$panel['ID']])) { $panel['ID']=bin2hex(random_bytes(8)); $changed=true; }
                $seen[$panel['ID']]=true;
            }
            unset($panel);
            if ($changed) { IPS_SetProperty($this->InstanceID,'Panels',json_encode($panels,JSON_THROW_ON_ERROR)); IPS_ApplyChanges($this->InstanceID); return; }
            foreach ($this->GetReferenceList() as $id) $this->UnregisterReference($id);
            $ids=[];
            foreach ($this->Phases() as $phase) foreach (['','Min','Max','Avg','Target'] as $suffix) $ids[]=$this->ReadPropertyInteger('Voltage'.$phase.$suffix);
            foreach ($panels as $p) {
                $ids[]=(int)$p['Target'];
                foreach (array_merge(['TotalPower'],array_map(fn($v)=>'Power'.$v,$this->Phases()),array_map(fn($v)=>'Current'.$v,$this->Phases())) as $key) {
                    $ids[]=(int)$p[$key];
                    if (!str_starts_with($key,'Current')) foreach (['Min','Max','Avg','Target','Export'] as $s) $ids[]=(int)$p[$key.$s];
                }
            }
            foreach (array_unique($ids) as $id) if ($id>1 && IPS_ObjectExists($id)) $this->RegisterReference($id);
            $this->SnapshotOrThrow(); $this->SetStatus(102);
        } catch (Throwable $e) { $this->SetStatus(200); }
        $this->Refresh();
    }
    private function Zones(float $start, mixed $rows): array
    {
        if (is_string($rows)) $rows=json_decode($rows,true,512,JSON_THROW_ON_ERROR);
        if (!is_array($rows) || count($rows)===0 || !is_finite($start)) throw new Exception('Mindestens eine Skalenzone erforderlich');
        $result=[];
        foreach ($rows as $row) {
            if (!isset($row['End'],$row['Color']) || !is_numeric($row['End']) || !is_finite((float)$row['End']) || (float)$row['End']<=$start || !is_numeric($row['Color']) || (int)$row['Color']<0 || (int)$row['Color']>0xFFFFFF) throw new Exception('Skalenzonen: aufsteigende Grenzen und gültige Farben erforderlich');
            $result[]=['from'=>$start,'to'=>(float)$row['End'],'color'=>sprintf('#%06X',(int)$row['Color'])]; $start=(float)$row['End'];
        }
        return $result;
    }
    private function ReadMeasurement(int $id): array
    {
        if ($id<=1) return ['assigned'=>false,'value'=>null,'updated'=>0];
        if (!IPS_VariableExists($id)) return ['assigned'=>true,'value'=>null,'updated'=>0];
        $v=IPS_GetVariable($id); $value=GetValue($id);
        return ['assigned'=>true,'value'=>in_array($v['VariableType'],[1,2],true)&&is_finite((float)$value)?(float)$value:null,'updated'=>$v['VariableUpdated']];
    }
    private function Measurement(string $key, array $mapping, int $factor): array
    {
        $power=str_contains($key,'Power');
        $mode=(int)($mapping[$key.'Mode']??0);
        if ($power && !in_array($mode,[0,1],true)) throw new Exception('Ungültige Leistungszuordnung');
        $d=$this->ReadMeasurement((int)($mapping[$key]??0));
        if ($power && $mode===1) {
            $other=$this->ReadMeasurement((int)($mapping[$key.'Export']??0));
            // An incomplete pair must never silently turn into a plausible net value.
            $assigned=$d['assigned'] || $other['assigned'];
            $valid=$d['assigned'] && $other['assigned'] && $d['value']!==null && $other['value']!==null && $d['value']>=0 && $other['value']>=0;
            $d=['assigned'=>$assigned,'value'=>$valid?$d['value']-$other['value']:null,'updated'=>min($d['updated'],$other['updated'])];
        }
        $invert=$power && (bool)($mapping[$key.'Invert']??false);
        $multiplier=$power?$factor*($invert?-1:1):1;
        if ($d['value']!==null) { $d['value']*=$multiplier; if (!is_finite($d['value'])) $d['value']=null; }
        if (!str_starts_with($key,'Current')) {
            $d['target']=(int)($mapping[$key.'Target']??1);
            // Statistics are supplied for the signed net value, before inversion.
            // Negating a series swaps its minimum and maximum.
            foreach (['Min','Max','Avg'] as $stat) {
                $input=$invert?(['Min'=>'Max','Max'=>'Min','Avg'=>'Avg'][$stat]):$stat;
                $v=$this->ReadMeasurement((int)($mapping[$key.$input]??0));
                if ($v['value']!==null) { $v['value']*=$multiplier; if (!is_finite($v['value'])) $v['value']=null; }
                $d[$stat]=$v;
            }
        }
        return $d;
    }
    private function PowerZones(mixed $rows, bool $bidirectional): array
    {
        $zones=$this->Zones(0.0,$rows);
        if (!$bidirectional) return $zones;
        $negative=[];
        foreach (array_reverse($zones) as $z) $negative[]=['from'=>-$z['to'],'to'=>-$z['from'],'color'=>$z['color']];
        return array_merge($negative,$zones);
    }
    private function SnapshotOrThrow(): array
    {
        $factor=$this->ReadPropertyInteger('PowerUnit'); if (!in_array($factor,[1,1000],true)) throw new Exception('Leistungseinheit prüfen');
        $config=['showVoltage'=>$this->ReadPropertyBoolean('ShowVoltage'),'timeout'=>max(0,$this->ReadPropertyInteger('Timeout')),'factor'=>$factor,'nominal'=>$this->ReadPropertyFloat('NominalVoltage'),'phases'=>count($this->Phases()),'zones'=>['Voltage'=>$this->Zones($this->ReadPropertyFloat('VoltageStart'),$this->ReadPropertyString('VoltageZones'))]];
        $voltage=[];
        foreach ($this->Phases() as $phase) {
            $key='Voltage'.$phase; $mapping=[];
            foreach (['','Min','Max','Avg','Target'] as $s) $mapping[$key.$s]=$this->ReadPropertyInteger($key.$s);
            $voltage[$key]=$this->Measurement($key,$mapping,1);
        }
        $panels=[];
        foreach ($this->Panels() as $i=>$p) {
            try {
                $zones=['Total'=>$this->PowerZones($p['TotalZones'],(bool)$p['TotalBidirectional']),'Phase'=>$this->PowerZones($p['PhaseZones'],(bool)$p['PhaseBidirectional'])];
                $data=[];
                foreach (array_merge(['TotalPower'],array_map(fn($v)=>'Power'.$v,$this->Phases()),array_map(fn($v)=>'Current'.$v,$this->Phases())) as $key) $data[$key]=$this->Measurement($key,$p,$factor);
                $panels[]=['id'=>$p['ID'],'name'=>(string)$p['Name'],'target'=>(int)$p['Target'],'fuse'=>max(0,(float)$p['Fuse']),'showTotal'=>(bool)$p['ShowTotal'],'showPhase'=>(bool)$p['ShowPhase'],'zones'=>$zones,'data'=>$data];
            } catch (Throwable $e) { throw new Exception('Panel '.($i+1).': '.$e->getMessage()); }
        }
        return ['config'=>$config,'voltage'=>$voltage,'panels'=>$panels,'serverTime'=>time()];
    }
    private function Snapshot(): array
    {
        try { return $this->SnapshotOrThrow(); } catch (Throwable $e) { return ['error'=>'Konfiguration prüfen: '.$e->getMessage()]; }
    }
    public function Refresh(): void { $this->UpdateVisualizationValue(json_encode($this->Snapshot(),JSON_INVALID_UTF8_SUBSTITUTE)); }
    public function RequestAction(string $Ident, mixed $Value): void { if ($Ident==='Refresh') $this->Refresh(); }
    public function GetVisualizationTile(): string
    {
        return str_replace('/*INITIAL*/','handleMessage('.json_encode($this->Snapshot(),JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_INVALID_UTF8_SUBSTITUTE).');',file_get_contents(__DIR__.'/module.html'));
    }
    private function VariableField(string $name, string $caption): array { return ['type'=>'SelectVariable','name'=>$name,'caption'=>$caption]; }
    private function TargetField(string $name, string $caption): array { return ['type'=>'SelectObject','name'=>$name,'caption'=>$caption,'value'=>1]; }
    private function MeasurementForm(string $key, string $caption): array
    {
        $stats=[]; foreach (['Min'=>'Minimum','Max'=>'Maximum','Avg'=>'Durchschnitt'] as $s=>$label) $stats[]=$this->VariableField($key.$s,$label.' (optional)');
        $items=[];
        if (str_contains($key,'Power')) {
            $items[]=['type'=>'Select','name'=>$key.'Mode','caption'=>'Messwertquelle','options'=>[['caption'=>'Eine Variable (mit Vorzeichen)','value'=>0],['caption'=>'Zwei Variablen (Bezug minus Abgabe)','value'=>1]]];
            $items[]=$this->VariableField($key,'Messwert / Bezug bei zwei Variablen');
            $items[]=$this->VariableField($key.'Export','Abgabe (nur bei zwei Variablen)');
            $items[]=['type'=>'CheckBox','name'=>$key.'Invert','caption'=>'Vorzeichen umkehren'];
            $items[]=['type'=>'Label','caption'=>'Bezug positiv, Abgabe negativ. Zwei Richtungswerte müssen jeweils >= 0 sein. Statistik: vorzeichenbehaftete Nettoleistung vor Vorzeichenumkehr, keine Richtungsstatistiken.'];
        } else $items[]=$this->VariableField($key,'Messwert');
        $items[]=$this->TargetField($key.'Target','Navigationsziel (optional)');
        $items[]=['type'=>'ExpansionPanel','caption'=>'Statistik (optional)','items'=>$stats];
        return ['type'=>'ExpansionPanel','caption'=>$caption,'items'=>$items];
    }
    private function ZoneForm(string $name, string $caption, bool $voltage): array
    {
        $items=[];
        if ($voltage) $items[]=['type'=>'NumberSpinner','name'=>'VoltageStart','caption'=>'Von (erste Zone, V)','digits'=>1];
        $items[]=['type'=>'Label','caption'=>$voltage?'Jede Zone beginnt am Ende der vorherigen. Letztes Bis = Skalenende.':'Die erste Zone beginnt bei 0 W. Jede weitere beginnt am Ende der vorherigen.'];
        $items[]=['type'=>'List','name'=>$name.'Zones','rowCount'=>4,'add'=>true,'delete'=>true,'columns'=>[
            ['caption'=>$voltage?'Bis (V)':'Bis (W)','name'=>'End','width'=>'auto','add'=>$voltage?280:30000,'edit'=>['type'=>'NumberSpinner','digits'=>1]],
            ['caption'=>'Farbe','name'=>'Color','width'=>'160px','add'=>0x39A76B,'edit'=>['type'=>'SelectColor']]]];
        return ['type'=>'ExpansionPanel','caption'=>$caption,'items'=>$items];
    }
    public function GetPanelForm(int $PhaseCount): string
    {
        $phases=array_slice(['A','B','C'],0,max(1,min(3,$PhaseCount)));
        $items=[['type'=>'ValidationTextBox','name'=>'Name','caption'=>'Panelname (optional bei einem Panel)'],$this->TargetField('Target','Navigationsziel des Panels (optional)'),['type'=>'NumberSpinner','name'=>'Fuse','caption'=>'Absicherung pro Phase (A)','digits'=>1,'minimum'=>0]];
        $items[]=['type'=>'ExpansionPanel','caption'=>'Gesamtleistung','items'=>[['type'=>'CheckBox','name'=>'ShowTotal','caption'=>'Gesamtleistung anzeigen'],['type'=>'CheckBox','name'=>'TotalBidirectional','caption'=>'Bidirektionale Skala (Null in der Mitte)'],$this->MeasurementForm('TotalPower','Variablen'),$this->ZoneForm('Total','Skalenzonen (W)',false)]];
        $power=[['type'=>'CheckBox','name'=>'ShowPhase','caption'=>'Phasenleistungen und zugehörige Stromstärken anzeigen'],['type'=>'CheckBox','name'=>'PhaseBidirectional','caption'=>'Bidirektionale Skala (Null in der Mitte)']]; foreach ($phases as $p) $power[]=$this->MeasurementForm('Power'.$p,'Phase '.$p);
        $power[]=$this->ZoneForm('Phase','Gemeinsame Skalenzonen (W)',false);
        $items[]=['type'=>'ExpansionPanel','caption'=>'Phasenleistungen','items'=>$power];
        $current=[]; foreach ($phases as $p) $current[]=$this->VariableField('Current'.$p,'Phase '.$p.' (optional)');
        $items[]=['type'=>'ExpansionPanel','caption'=>'Stromstärken (optional)','items'=>$current];
        return json_encode($items,JSON_INVALID_UTF8_SUBSTITUTE);
    }
    public function GetConfigurationForm(): string
    {
        $module=[['type'=>'NumberSpinner','name'=>'Timeout','caption'=>'Aktualitätsgrenze (Sekunden, 0 = aus)','minimum'=>0],
            ['type'=>'Select','name'=>'PowerUnit','caption'=>'Einheit aller Leistungsvariablen inkl. Statistik','options'=>[['caption'=>'W','value'=>1],['caption'=>'kW','value'=>1000]]],
            ['type'=>'NumberSpinner','name'=>'NominalVoltage','caption'=>'Nennspannung (V)','digits'=>1,'minimum'=>1],
            ['type'=>'Select','name'=>'PhaseCount','caption'=>'Anzahl Phasen','options'=>[['caption'=>'1','value'=>1],['caption'=>'2','value'=>2],['caption'=>'3','value'=>3]],'onChange'=>'PWRCP_ShowPhases($id, $PhaseCount);']];
        $voltage=[['type'=>'CheckBox','name'=>'ShowVoltage','caption'=>'Spannungen anzeigen']];
        foreach (['A','B','C'] as $i=>$p) { $f=$this->MeasurementForm('Voltage'.$p,'Phase '.$p); $f['name']='VoltagePhase'.$p; $f['visible']=$i<count($this->Phases()); $voltage[]=$f; }
        $voltage[]=$this->ZoneForm('Voltage','Gemeinsame Skalenzonen (V)',true);
        $columns=[];
        foreach ($this->PanelDefaults() as $name=>$value) {
            $c=['name'=>$name,'caption'=>$name==='Name'?'Panelname':$name,'width'=>$name==='Name'?'auto':'100px','visible'=>$name==='Name','save'=>true,'add'=>$value];
            if (str_ends_with($name,'Zones')) $c['edit']=['type'=>'List'];
            $columns[]=$c;
        }
        $list=['type'=>'List','name'=>'Panels','caption'=>'Leistungspanels','rowCount'=>5,'add'=>true,'delete'=>true,'changeOrder'=>true,'columns'=>$columns,'form'=>'return json_decode(PWRCP_GetPanelForm($id, $PhaseCount), true);'];
        if ($this->ReadPropertyString('Panels')==='') { $list['loadValuesFromConfiguration']=false; $list['values']=[$this->LegacyPanel()]; }
        $elements=[['type'=>'Label','caption'=>'PowerCockpit 1.0 Build 12'],['type'=>'ExpansionPanel','caption'=>'Modul-Einstellungen','items'=>$module],['type'=>'ExpansionPanel','caption'=>'Spannungen (gemeinsam für alle Panels)','items'=>$voltage],$list,['type'=>'Label','caption'=>'Panels über das Bearbeiten-Symbol konfigurieren. Reihenfolge per Ziehen ändern. Änderungen werden mit Übernehmen gespeichert.']];
        return json_encode(['elements'=>$elements,'actions'=>[],'status'=>[['code'=>200,'icon'=>'error','caption'=>'Ungültige Konfiguration']]],JSON_INVALID_UTF8_SUBSTITUTE);
    }
    public function ShowPhases(int $PhaseCount): void
    {
        foreach (['A','B','C'] as $i=>$p) $this->UpdateFormField('VoltagePhase'.$p,'visible',$i<max(1,min(3,$PhaseCount)));
    }
}
