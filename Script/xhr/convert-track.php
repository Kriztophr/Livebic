<?php
if (empty($_POST["file"]) || empty($_POST["id"]) || IS_LOGGED == false) {
    exit();
}

runPlugin('PreSongConverted', ["audio_file" => $_POST["file"], "song_id" => $_POST["id"]]);

$request[] = !in_array($_POST["file"], $_SESSION["uploads"]);
$request[] = !file_exists($_POST["file"]);
if (in_array(true, $request)) {
    $errors[] = lang("Something went wrong Please try again later!");
} else {
    $ffmpeg_b = $music->config->ffmpeg_binary_file;
    $filepath = explode(".", $_POST["file"])[0];
    $time = time();
    $full_dir = str_replace("xhr", "/", __DIR__);
    if (!file_exists("upload/waves/" . date("Y"))) {
        @mkdir("upload/waves/" . date("Y"), 0777, true);
    }
    if (!file_exists("upload/waves/" . date("Y") . "/" . date("m"))) {
        @mkdir("upload/waves/" . date("Y") . "/" . date("m"), 0777, true);
    }
    $demoURL = $filepath . "_" . rand(11111, 99999) . "_converted.mp3";
    $originalURL = $filepath . "_" . rand(11111, 99999) . "_converted.mp3";

    $audio_output_mp3 = $full_dir . $originalURL;
    $audio_demo_output_mp3 = $full_dir . $demoURL;

    $backGround = false;
    if (empty($_POST["type"])) {
        $backGround = true;
    }
    if ($backGround == true) {
        ob_end_clean();
        $data = ["status" => 300];
        header("Content-Encoding: none");
        header("Connection: close");
        ignore_user_abort();
        ob_start();
        if (!empty($data)) {
            header("Content-Type: application/json");
            echo json_encode($data);
        }
        $size = ob_get_length();
        header("Content-Length: $size");
        ob_end_flush();
        flush();
        session_write_close();
        if (is_callable("fastcgi_finish_request")) {
            fastcgi_finish_request();
        }
    }

    $key = generateKey(40, 40);
    $generateWaveLight =
        "upload/waves/" .
        date("Y") .
        "/" .
        date("m") .
        "/" .
        $key .
        "_light.png";
    $generateWaveDark =
        "upload/waves/" .
        date("Y") .
        "/" .
        date("m") .
        "/" .
        $key .
        "_dark.png";
    $generateWaveDay =
        "upload/waves/" . date("Y") . "/" . date("m") . "/" . $key . "_day.png";

    $generateBuyerWaveLight =
        "upload/waves/" .
        date("Y") .
        "/" .
        date("m") .
        "/" .
        $key .
        "_demo_light.png";
    $generateBuyerWaveDark =
        "upload/waves/" .
        date("Y") .
        "/" .
        date("m") .
        "/" .
        $key .
        "_demo_dark.png";
    $generateBuyerWaveDay =
        "upload/waves/" .
        date("Y") .
        "/" .
        date("m") .
        "/" .
        $key .
        "_demo_day.png";

    $audio_output_light_wave = $full_dir . $generateWaveLight;
    $audio_output_black_wave = $full_dir . $generateWaveDark;
    $audio_output_day_wave = $full_dir . $generateWaveDay;

    $audio_output_demo_light_wave = $full_dir . $generateBuyerWaveLight;
    $audio_output_demo_black_wave = $full_dir . $generateBuyerWaveDark;
    $audio_output_demo_day_wave = $full_dir . $generateBuyerWaveDay;

    $audio_file_full_path = $full_dir . $_POST["file"];

    $wavecolor = $music->config->waves_color;

    $there_is_demo = false;

    $convert_speed_array = [
        "ultrafast" => 100,
        "superfast" => 120,
        "veryfast" => 150,
        "faster" => 192,
        "fast" => 200,
        "medium" => 220,
        "slow" => 240,
        "slower" => 260,
        "veryslow" => 280,
    ];
    $convert_speed = $convert_speed_array[$music->config->convert_speed];

    $shell = shell_exec(
        "$ffmpeg_b -i $audio_file_full_path -map 0:a:0 -b:a " .
            $convert_speed .
            "k $audio_output_mp3 2>&1"
    );
    $shell_get_time = shell_exec(
        "$ffmpeg_b -v quiet -stats -i $audio_output_mp3 -f null - 2>&1"
    );
    if (!empty($shell_get_time)) {
        $whatIWant = substr(
            $shell_get_time,
            strpos($shell_get_time, "time=") + 5
        );
        $timeOfAudioFile = substr($whatIWant, 0, strpos($whatIWant, " "));
        $seconds = strtotime("1970-01-01 $timeOfAudioFile UTC");
        $percentage = 25;
        $new_duration = ($percentage / 100) * $seconds;
        if (!empty($new_duration)) {
            $shell_make_demo = shell_exec(
                "$ffmpeg_b -t $new_duration -i $audio_output_mp3 -acodec copy $audio_demo_output_mp3 2>&1"
            );
            $there_is_demo = true;
        }
    }

    $shell = shell_exec(
        "$ffmpeg_b -y -i $audio_output_mp3 -filter_complex \"aformat=channel_layouts=mono,showwavespic=s=1100x150:colors=#6d6d6d\" -frames:v 1 $audio_output_black_wave 2>&1"
    );

    $shell = shell_exec(
        "$ffmpeg_b -y -i $audio_output_mp3 -filter_complex \"aformat=channel_layouts=mono,showwavespic=s=1100x150:colors=" .
            $wavecolor .
            "\" -frames:v 1 $audio_output_light_wave 2>&1"
    );
    $shell = shell_exec(
        "$ffmpeg_b -y -i $audio_output_mp3 -filter_complex \"aformat=channel_layouts=mono,showwavespic=s=1100x150:colors=#e5e5e5\" -frames:v 1 $audio_output_day_wave 2>&1"
    );
    if ($there_is_demo == true) {
        $shell = shell_exec(
        "$ffmpeg_b -y -i $audio_demo_output_mp3 -filter_complex \"aformat=channel_layouts=mono,showwavespic=s=1100x150:colors=#6d6d6d\" -frames:v 1 $audio_output_demo_black_wave 2>&1"
        );

        $shell = shell_exec(
            "$ffmpeg_b -y -i $audio_demo_output_mp3 -filter_complex \"aformat=channel_layouts=mono,showwavespic=s=1100x150:colors=" .
                $wavecolor .
                "\" -frames:v 1 $audio_output_demo_light_wave 2>&1"
        );
        $shell = shell_exec(
            "$ffmpeg_b -y -i $audio_demo_output_mp3 -filter_complex \"aformat=channel_layouts=mono,showwavespic=s=1100x150:colors=#e5e5e5\" -frames:v 1 $audio_output_demo_day_wave 2>&1"
        );

    }
    

    $uploadConvertedSong = PT_UploadToS3($originalURL);
    if ($music->config->google_drive == "on") {
        $originalURL = $uploadConvertedSong;
    }
    if ($there_is_demo == true) {
        $uploadDemoSong = PT_UploadToS3($demoURL);
        if ($music->config->google_drive == "on") {
            $demoURL = $uploadDemoSong;
        }
        $update_data["demo_track"] = $demoURL;
        $hours = floor($new_duration / 3600);
        $mins = floor(($new_duration / 60) % 60);
        $secs = floor($new_duration % 60);
        $timeFormat = sprintf("%02d:%02d:%02d", $hours, $mins, $secs);
        $update_data["demo_duration"] = $timeFormat;
        PT_UploadToS3($generateBuyerWaveLight);
        PT_UploadToS3($generateBuyerWaveDark);
        PT_UploadToS3($generateBuyerWaveDay);
    }

    if (
        !empty($_POST["type"]) &&
        $_POST["type"] == "story" &&
        is_numeric($_POST["id"])
    ) {
        $db->where("id", Secure($_POST["id"]))->where(
            "user_id",
            $music->user->id
        );
        $update_data = [
            "audio" => $originalURL,
        ];

        $db->update(T_STORY, $update_data);
    } else {
        $db->where("audio_id", $_POST["id"]);
        $update_data = [
            "audio_location" => $originalURL,
            'converted' => 1
        ];

        if (file_exists($generateWaveLight) && file_exists($generateWaveDark)) {
            $update_data["dark_wave"] = $generateWaveDark;
            $update_data["light_wave"] = $generateWaveLight;
        }

        $db->update(T_SONGS, $update_data);
    }

    if (file_exists($_POST["file"])) {
        unlink($_POST["file"]);
    }

    if (isset($_SESSION["uploads"]) && !isset($_GET["reset"])) {
        unset($_SESSION["uploads"]);
    }

    if (file_exists($audio_output_mp3)) {
        $size = filesize($audio_output_mp3) / 1000;
        $update = $db
            ->where("id", $user->id)
            ->update(T_USERS, ["uploads" => $db->inc($size)]);
    }
    PT_UploadToS3($generateWaveLight);
    PT_UploadToS3($generateWaveDark);
    PT_UploadToS3($generateWaveDay);
    

    if (!isset($_GET["reset"])) {
        $_SESSION["uploads"] = [];
    }
    $getSongData = $db->where("audio_id", secure($_POST["id"]))->getOne(T_SONGS);
    $db->where('file_name', secure($_POST['file']))->delete(T_UPLOADS);
    if (empty($_POST["type"])) {
        RecordUserActivities("upload", $update_data);
    }

    runPlugin('AfterSongConverted', ["audio_file" => $audio_output_mp3, "song_id" => $_POST["id"]]);

    $data = ["status" => 200, "audio_file" => $audio_output_mp3];
}
?>