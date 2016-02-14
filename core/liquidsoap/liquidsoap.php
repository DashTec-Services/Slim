<?php
/**
 * Created by David Schomburg (DashTec - Services)
 *      www.dashtec.de
 *
 *  S:P (StreamersPanel)
 *  Support: http://board.streamerspanel.de
 *
 *  v 4.5.0
 *
 *  Kundennummer:   @KDNUM@
 *  Lizenznummer:   @RECHNR@
 *  Lizenz: http://login.streamerspanel.de/user/terms
 */
namespace core\liquidsoap;


class liquidsoap
{

    public function createLiquiteConf($IceRelId){

        # ICECAST BASE CONF
        $file = __DIR__."/../../templates/liquid_wo_out_timelist.dsd";



        # Ices-Rel
        $IcesRel = \DB::queryFirstRow("SELECT * FROM icecast_rel WHERE id=%s", $IceRelId);

        # IceCast_Conf
        $ICEVar = \DB::queryFirstRow("SELECT * FROM icecast_serv WHERE id=%s", $IcesRel['icecast_serv_id']);

        # StreamersPanel_Conf
        $ServConf = \DB::queryFirstRow("SELECT * FROM config WHERE id=%s", '1');

        # Ices Conf
        $icecDBconf = \DB::queryFirstRow("SELECT * FROM liquid WHERE id=%s", $IcesRel['liquid_id']);

## Set Var


        $logf = $ServConf['doc_root'].'/streamconf/'.$ICEVar['port'].'/stream.log';
        $content = str_replace('@Log@', $logf, $file);
        $content = str_replace('@Encode@', $icecDBconf['streamAs'], $content);
        $content = str_replace('@File@', $icecDBconf['myplaylist'], $content);
        $content = str_replace('@Port@', $ICEVar['port'], $content);
        $content = str_replace('@Password@', $ICEVar['source-password'], $content);
        $content = str_replace('@Name@', $icecDBconf['name'], $content);
        $content = str_replace('@Description@', $icecDBconf['description'], $content);
        $content = str_replace('@URL@', $icecDBconf['url'], $content);

# Create File
        $dateihandle = fopen("streamconf/" . $ICEVar['port'] . "/conf.liq","w");
        fwrite($dateihandle, $content);

        chmod("streamconf/" . $ICEVar['port'] . "/conf.liq",0755);

    }


    public function createPlaylst($IceRelId)
    {

# Einlesen der sc_rel Daten
        $IceCastRel = \DB::queryFirstRow("SELECT * FROM icecast_rel WHERE id=%s", $IceRelId);

# Einlesen der Datenbank
        $Sc_Playlist = \DB::queryFirstRow("SELECT * FROM playlist WHERE id=%s", $IceCastRel['playlist_id']);

# Port des SC_Serv ermitteln
        $SC_Port_Base = \DB::queryFirstRow("SELECT port FROM icecast_serv WHERE id=%s", $IceCastRel['icecast_serv_id']);
        $datei = fopen("streamconf/" . $SC_Port_Base['port'] . "/" . $Sc_Playlist['playlist_name'] . ".lst", "w+");

# ColumList aus der Datenbank
        $columns = \DB::query("SELECT mp3_id FROM playlist_mp3_rel WHERE playlist_id=%s", $Sc_Playlist['id']);
        foreach ($columns as $mp3) {
            $mp3_name = \DB::query("SELECT dir_titel FROM mp3 WHERE id=%s", $mp3['mp3_id']);
            foreach ($mp3_name as $name) {
                fwrite($datei, $_SERVER['DOCUMENT_ROOT'] . "/mp3collection/" . $name['dir_titel'] . "\r\n");
            }
        }
        fclose($datei); # Datei schlieÃŸen




    }

    public function getSSHConf()
    {

        $config = \DB::queryFirstRow("SELECT * FROM config WHERE id='1'");

        $SSHConf['ip'] = $config['server_ip'];
        $SSHConf['user'] = $config['root_user'];
        $SSHConf['pass'] = $config['root_password'];
        $SSHConf['port'] = $config['ssh_port'];

        return $SSHConf;
    }

    public function startLiquid($IceRelId){

        $this->createLiquiteConf($IceRelId);

        // SSH AUSFÃœHREN
        $SSHConf = $this->getSSHConf();
        $connection = ssh2_connect($SSHConf['ip'], $SSHConf['port']);
        ssh2_auth_password($connection, $SSHConf['user'], $SSHConf['pass']);
        $IceCastRel = \DB::queryFirstRow("SELECT * FROM icecast_rel WHERE id=%s", $IceRelId);
        $SC_Port_Base = \DB::queryFirstRow("SELECT port FROM icecast_serv WHERE id=%s", $IceCastRel['icecast_serv_id']);
        $ssh2_exec_com =  ssh2_exec($connection, 'liquidsoap' . ' ' . $_SERVER['DOCUMENT_ROOT'] . '/streamconf/' . $SC_Port_Base['port'] . '/conf.liq </dev/null 2>&1 & echo $!;');
        sleep(3);

        $pid = stream_get_contents($ssh2_exec_com);

        \DB::update('icecast_rel', array(
            'liquid_pid' => $pid,
        ), "id=%s", $IceRelId);

    }

    public function killSc_liquid($IceRelId)
    {
        $PID = \DB::queryFirstRow("SELECT liquid_pid FROM icecast_rel WHERE id=%s", $IceRelId);
        $SSHConf = $this->getSSHConf();
        $connection = ssh2_connect($SSHConf['ip'], $SSHConf['port']);
        ssh2_auth_password($connection, $SSHConf['user'], $SSHConf['pass']);
        ssh2_exec($connection, 'kill -9 ' . $PID['liquid_pid']);
        sleep(3);
        $this->setPID($IceRelId, '0');
    }

    private function setPID($IceRelId, $PID)
    {
        \DB::update('icecast_rel', array(
            'liquid_pid' => $PID
        ), "id=%s", $IceRelId);
    }





}