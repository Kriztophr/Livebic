<?php
if (file_exists('./assets/init.php')) {
    require_once('./assets/init.php');
} else {
    die('Please put this file in the home directory !');
}
function PT_UpdateLangs($lang, $key, $value) {
    global $sqlConnect;
    $update_query         = "UPDATE langs SET `{lang}` = '{lang_text}' WHERE `lang_key` = '{lang_key}'";
    $update_replace_array = array(
        "{lang}",
        "{lang_text}",
        "{lang_key}"
    );
    return str_replace($update_replace_array, array(
        $lang,
        mysqli_real_escape_string($sqlConnect, $value),
        $key
    ), $update_query);
}
$updated = false;
if (!empty($_GET['updated'])) {
    $updated = true;
}
if (!empty($_POST['query'])) {
    $query = mysqli_query($mysqli, base64_decode($_POST['query']));
    if ($query) {
        $data['status'] = 200;
    } else {
        $data['status'] = 400;
        $data['error']  = mysqli_error($mysqli);
    }
    header("Content-type: application/json");
    echo json_encode($data);
    exit();
}
if (!empty($_POST['update_langs'])) {
    $data  = array();
    $query = mysqli_query($sqlConnect, "SHOW COLUMNS FROM `langs`");
    while ($fetched_data = mysqli_fetch_assoc($query)) {
        $data[] = $fetched_data['Field'];
    }
    unset($data[0]);
    unset($data[1]);
    unset($data[2]);
    unset($data[3]);
    $lang_update_queries = array();
    foreach ($data as $key => $value) {
        $value = ($value);
        if ($value == 'arabic') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song_alert', 'يلزم تحميل أغنية واحدة على الأقل لنشر منتج ، يمكنك {LINK}.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song', 'تحميل اغنية جديدة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'send_again', 'إعادة إرسال');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_two_expired', 'انتهت صلاحية الرمز ، يرجى محاولة تسجيل الدخول مرة أخرى');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_cant_send_now', 'الرجاء الانتظار بضع ثوان قبل طلب ارتباط جديد.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_successfully_sent', 'تم إرسال الرمز بنجاح');
            $lang_update_queries[] = PT_UpdateLangs($value, 'mark_all_as_read', 'اشر عليها بانها قرات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'withdraw_method', 'طريقة السحب');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank', 'بنك');
            $lang_update_queries[] = PT_UpdateLangs($value, 'skrill', 'سكريل');
            $lang_update_queries[] = PT_UpdateLangs($value, 'transfer_to', 'حول إلى');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iban', 'ذاهبون');
            $lang_update_queries[] = PT_UpdateLangs($value, 'swift_code', 'رمز السرعة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_payment_method', 'الرجاء اختيار طريقة الدفع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'week', 'أسبوع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'unlimited', 'غير محدود');
            $lang_update_queries[] = PT_UpdateLangs($value, 'featured_member', 'عضو مميز');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_badge', 'تم التحقق من الشارة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'discount', 'تخفيض');
            $lang_update_queries[] = PT_UpdateLangs($value, 'max_upload', 'ماكس تحميل');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_download', 'تحميل اغاني');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_station_import', 'محطة استيراد');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_sell', 'بيع الأغاني');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_multi_upload', 'تحميل أغاني متعددة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_channel_trailer', 'تحميل المقطع الدعائي للقناة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_blog', 'انشاء مدونة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_upload', 'تحميل الأغاني');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_soundcloud_import', 'استيراد Soundcloud');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_deezer_import', 'استيراد Deezer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_import', 'اي تيونز للاستيراد');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_affiliate', 'انضم اي تيونز');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_youtube_import', 'استيراد يوتيوب');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_user_ads', 'إنشاء إعلانات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_audio_ads', 'إنشاء إعلانات صوتية');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_event_system', 'إنشاء الأحداث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_story_system', 'إنشاء قصة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_store_system', 'بيع المنتجات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'after_artist', 'يمكنك استخدام هذه الميزات بمجرد أن يصبح حسابك فنانًا.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'category_can_not_be_empty', 'لا يمكن أن تكون الفئة فارغة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pro_package', 'حزمة Pro');
            $lang_update_queries[] = PT_UpdateLangs($value, 'select_pro_package', 'حدد باقة Pro');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_point_system', 'اكسب النقاط');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_donate_system', 'تبرع زر');
        } else if ($value == 'dutch') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song_alert', 'Er is ten minste één geüpload nummer vereist om een product te plaatsen, je kunt {LINK}.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song', 'een nieuw nummer uploaden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'send_again', 'Opnieuw verzenden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_two_expired', 'Code verlopen probeer opnieuw in te loggen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_cant_send_now', 'Wacht een paar seconden voordat u een nieuwe link aanvraagt.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_successfully_sent', 'Code succesvol verzonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'mark_all_as_read', 'Markeer alles als gelezen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'withdraw_method', 'Opnamemethode');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank', 'Bank');
            $lang_update_queries[] = PT_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = PT_UpdateLangs($value, 'transfer_to', 'Overzetten naar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iban', 'We gaan');
            $lang_update_queries[] = PT_UpdateLangs($value, 'swift_code', 'Swift code');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_payment_method', 'Selecteer een betaalmethode, alstublieft');
            $lang_update_queries[] = PT_UpdateLangs($value, 'week', 'Week');
            $lang_update_queries[] = PT_UpdateLangs($value, 'unlimited', 'Onbeperkt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'featured_member', 'Aanbevolen lid');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_badge', 'Geverifieerde badge');
            $lang_update_queries[] = PT_UpdateLangs($value, 'discount', 'Korting');
            $lang_update_queries[] = PT_UpdateLangs($value, 'max_upload', 'Maximale upload');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_download', 'Liedjes downloaden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_station_import', 'Importstation');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_sell', 'Liedjes verkopen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_multi_upload', 'Meerdere nummers uploaden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_channel_trailer', 'Kanaaltrailer uploaden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_blog', 'Blog maken');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_upload', 'Nummers uploaden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_soundcloud_import', 'Soundcloud importeren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_deezer_import', 'Deezer Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_import', 'Itunes importeren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_affiliate', 'Itunes-partner');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_youtube_import', 'YouTube-import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_user_ads', 'Advertenties maken');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_audio_ads', 'Audioadvertenties maken');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_event_system', 'Evenementen maken');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_story_system', 'Verhaal maken');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_store_system', 'Producten verkopen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'after_artist', 'U kunt deze functies gebruiken zodra uw account een artiest is geworden.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'category_can_not_be_empty', 'Categorie mag niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pro_package', 'Pro-pakket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'select_pro_package', 'Selecteer Pro-pakket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_point_system', 'Verdien punten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_donate_system', 'Doneer knop');
        } else if ($value == 'french') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song_alert', 'Au moins une chanson téléchargée est requise pour publier un produit, vous pouvez {LINK}.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song', 'télécharger une nouvelle chanson');
            $lang_update_queries[] = PT_UpdateLangs($value, 'send_again', 'Renvoyer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_two_expired', 'Code expiré, veuillez réessayer de vous connecter');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_cant_send_now', 'Veuillez patienter quelques secondes avant de demander un nouveau lien.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_successfully_sent', 'Code envoyé avec succès');
            $lang_update_queries[] = PT_UpdateLangs($value, 'mark_all_as_read', 'tout marquer comme lu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'withdraw_method', 'Méthode de retrait');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank', 'Banque');
            $lang_update_queries[] = PT_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = PT_UpdateLangs($value, 'transfer_to', 'Transférer à');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iban', 'Allaient');
            $lang_update_queries[] = PT_UpdateLangs($value, 'swift_code', 'Code rapide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_payment_method', 'Veuillez choisir un moyen de paiement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'week', 'La semaine');
            $lang_update_queries[] = PT_UpdateLangs($value, 'unlimited', 'Illimité');
            $lang_update_queries[] = PT_UpdateLangs($value, 'featured_member', 'Membre en vedette');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_badge', 'Insigne vérifié');
            $lang_update_queries[] = PT_UpdateLangs($value, 'discount', 'Remise');
            $lang_update_queries[] = PT_UpdateLangs($value, 'max_upload', 'Téléchargement maximal');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_download', 'Télécharger des chansons');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_station_import', 'Poste d&#39;importation');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_sell', 'Vendre des chansons');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_multi_upload', 'Télécharger plusieurs chansons');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_channel_trailer', 'Mettre en ligne la bande-annonce de la chaîne');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_blog', 'Créer un blog');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_upload', 'Télécharger des chansons');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_soundcloud_import', 'Importation Soundcloud');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_deezer_import', 'Importation Deezer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_import', 'Importation d&#39;Itunes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_affiliate', 'Affilié Itunes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_youtube_import', 'Importation YouTube');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_user_ads', 'Créer des annonces');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_audio_ads', 'Créer des annonces audio');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_event_system', 'Créer des événements');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_story_system', 'Créer une histoire');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_store_system', 'Vendez des produits');
            $lang_update_queries[] = PT_UpdateLangs($value, 'after_artist', 'Vous pouvez utiliser ces fonctionnalités une fois que votre compte est devenu un artiste.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'category_can_not_be_empty', 'La catégorie ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pro_package', 'Forfait Pro');
            $lang_update_queries[] = PT_UpdateLangs($value, 'select_pro_package', 'Sélectionnez le forfait Pro');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_point_system', 'Gagnez des points');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_donate_system', 'Bouton Faire un don');
        } else if ($value == 'german') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song_alert', 'Mindestens ein hochgeladener Song ist erforderlich, um ein Produkt zu posten, Sie können {LINK}.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song', 'Lade ein neues Lied hoch');
            $lang_update_queries[] = PT_UpdateLangs($value, 'send_again', 'Erneut senden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_two_expired', 'Code abgelaufen, bitte versuchen Sie sich erneut anzumelden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_cant_send_now', 'Bitte warten Sie einige Sekunden, bevor Sie einen neuen Link anfordern.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_successfully_sent', 'Code erfolgreich gesendet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'mark_all_as_read', 'Alles als gelesen markieren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'withdraw_method', 'Methode zurückziehen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank', 'Bank');
            $lang_update_queries[] = PT_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = PT_UpdateLangs($value, 'transfer_to', 'Überweisung an');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iban', 'Wir gehen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'swift_code', 'SWIFT-Code');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_payment_method', 'Bitte Zahlungsart wählen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'week', 'Woche');
            $lang_update_queries[] = PT_UpdateLangs($value, 'unlimited', 'Unbegrenzt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'featured_member', 'Ausgewähltes Mitglied');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_badge', 'Verifiziertes Abzeichen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'discount', 'Rabatt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'max_upload', 'Maximaler Upload');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_download', 'Songs herunterladen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_station_import', 'Station importieren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_sell', 'Lieder verkaufen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_multi_upload', 'Laden Sie mehrere Songs hoch');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_channel_trailer', 'Kanal-Trailer hochladen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_blog', 'Blog erstellen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_upload', 'Lieder hochladen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_soundcloud_import', 'Soundcloud-Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_deezer_import', 'Deezer-Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_import', 'iTunes-Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_affiliate', 'iTunes-Affiliate');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_youtube_import', 'Youtube-Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_user_ads', 'Anzeigen erstellen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_audio_ads', 'Erstellen Sie Audio-Anzeigen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_event_system', 'Ereignisse erstellen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_story_system', 'Geschichte erstellen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_store_system', 'Produkte verkaufen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'after_artist', 'Sie können diese Funktionen nutzen, sobald Ihr Konto ein Künstler geworden ist.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'category_can_not_be_empty', 'Kategorie darf nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pro_package', 'Pro-Paket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'select_pro_package', 'Wählen Sie Pro-Paket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_point_system', 'Verdiene Punkte');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_donate_system', 'Spenden-Button');
        } else if ($value == 'russian') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song_alert', 'Для публикации продукта требуется хотя бы одна загруженная песня, вы можете {LINK}.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song', 'загрузить новую песню');
            $lang_update_queries[] = PT_UpdateLangs($value, 'send_again', 'Отправить');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_two_expired', 'Срок действия кода истек, попробуйте войти еще раз');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_cant_send_now', 'Пожалуйста, подождите несколько секунд, прежде чем запрашивать новую ссылку.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_successfully_sent', 'Код успешно отправлен');
            $lang_update_queries[] = PT_UpdateLangs($value, 'mark_all_as_read', 'отметить все как прочитанное');
            $lang_update_queries[] = PT_UpdateLangs($value, 'withdraw_method', 'Способ вывода');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank', 'Банк');
            $lang_update_queries[] = PT_UpdateLangs($value, 'skrill', 'Скрилл');
            $lang_update_queries[] = PT_UpdateLangs($value, 'transfer_to', 'Перевести в');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iban', 'Собирались');
            $lang_update_queries[] = PT_UpdateLangs($value, 'swift_code', 'Свифт-код');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_payment_method', 'Пожалуйста, выберите способ оплаты');
            $lang_update_queries[] = PT_UpdateLangs($value, 'week', 'Неделя');
            $lang_update_queries[] = PT_UpdateLangs($value, 'unlimited', 'Неограниченный');
            $lang_update_queries[] = PT_UpdateLangs($value, 'featured_member', 'Избранный участник');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_badge', 'Подтвержденный значок');
            $lang_update_queries[] = PT_UpdateLangs($value, 'discount', 'Скидка');
            $lang_update_queries[] = PT_UpdateLangs($value, 'max_upload', 'Максимальная загрузка');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_download', 'Скачать песни');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_station_import', 'Станция импорта');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_sell', 'Продавать песни');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_multi_upload', 'Загрузить несколько песен');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_channel_trailer', 'Загрузить трейлер канала');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_blog', 'Создать блог');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_upload', 'Загрузить песни');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_soundcloud_import', 'Импорт из саундклауда');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_deezer_import', 'Дезер Импорт');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_import', 'Импорт iTunes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_affiliate', 'Партнерская программа iTunes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_youtube_import', 'Ютуб Импорт');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_user_ads', 'Создать рекламу');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_audio_ads', 'Создать аудиообъявления');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_event_system', 'Создать события');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_story_system', 'Создать историю');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_store_system', 'Продавать продукты');
            $lang_update_queries[] = PT_UpdateLangs($value, 'after_artist', 'Вы сможете использовать эти функции, как только ваша учетная запись станет артистической.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'category_can_not_be_empty', 'Категория не может быть пустой');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pro_package', 'Профессиональный пакет');
            $lang_update_queries[] = PT_UpdateLangs($value, 'select_pro_package', 'Выберите профессиональный пакет');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_point_system', 'Зарабатывайте очки');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_donate_system', 'Кнопка пожертвования');
        } else if ($value == 'spanish') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song_alert', 'Se requiere al menos una canción cargada para publicar un producto, puedes {ENLACE}.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song', 'sube una nueva canción');
            $lang_update_queries[] = PT_UpdateLangs($value, 'send_again', 'reenviar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_two_expired', 'Código caducado, intente iniciar sesión de nuevo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_cant_send_now', 'Espere unos segundos antes de solicitar un nuevo enlace.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_successfully_sent', 'Código enviado con éxito');
            $lang_update_queries[] = PT_UpdateLangs($value, 'mark_all_as_read', 'marcar todo como leido');
            $lang_update_queries[] = PT_UpdateLangs($value, 'withdraw_method', 'método de retiro');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank', 'Banco');
            $lang_update_queries[] = PT_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = PT_UpdateLangs($value, 'transfer_to', 'Transferir a');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iban', 'Iban');
            $lang_update_queries[] = PT_UpdateLangs($value, 'swift_code', 'Código SWIFT');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_payment_method', 'Por favor seleccione un método de pago');
            $lang_update_queries[] = PT_UpdateLangs($value, 'week', 'Semana');
            $lang_update_queries[] = PT_UpdateLangs($value, 'unlimited', 'Ilimitado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'featured_member', 'Miembro destacado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_badge', 'Insignia verificada');
            $lang_update_queries[] = PT_UpdateLangs($value, 'discount', 'Descuento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'max_upload', 'Carga máxima');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_download', 'Descargar canciones');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_station_import', 'Estación de importación');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_sell', 'Vender canciones');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_multi_upload', 'Subir varias canciones');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_channel_trailer', 'Subir tráiler del canal');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_blog', 'Blog creativo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_upload', 'Subir canciones');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_soundcloud_import', 'Importación de Soundcloud');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_deezer_import', 'Importación de Deezer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_import', 'Importación de iTunes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_affiliate', 'Afiliado de iTunes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_youtube_import', 'Importación de Youtube');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_user_ads', 'Crear anuncios');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_audio_ads', 'Crear anuncios de audio');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_event_system', 'Crear eventos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_story_system', 'Crear historia');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_store_system', 'Vender productos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'after_artist', 'Puede usar esas funciones una vez que su cuenta se convierta en artista.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'category_can_not_be_empty', 'La categoría no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pro_package', 'Paquete profesional');
            $lang_update_queries[] = PT_UpdateLangs($value, 'select_pro_package', 'Seleccionar paquete profesional');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_point_system', 'Gana puntos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_donate_system', 'botón donar');
        } else if ($value == 'turkish') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song_alert', 'Bir ürünü yayınlamak için en az bir yüklenmiş şarkı gerekir, {LINK} yapabilirsiniz.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song', 'yeni bir şarkı yükle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'send_again', 'Yeniden gönder');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_two_expired', 'Kodun süresi doldu lütfen tekrar giriş yapmayı deneyin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_cant_send_now', 'Lütfen yeni bir bağlantı istemeden önce birkaç saniye bekleyin.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_successfully_sent', 'Kod başarıyla gönderildi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'mark_all_as_read', 'Tümünü okundu olarak işaretle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'withdraw_method', 'Geri çekme yöntemi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank', 'Banka');
            $lang_update_queries[] = PT_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = PT_UpdateLangs($value, 'transfer_to', 'Transfer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iban', 'gidiyordu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'swift_code', 'Swift kodu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_payment_method', 'Lütfen bir ödeme yöntemi seçin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'week', 'Hafta');
            $lang_update_queries[] = PT_UpdateLangs($value, 'unlimited', 'Sınırsız');
            $lang_update_queries[] = PT_UpdateLangs($value, 'featured_member', 'Öne Çıkan Üye');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_badge', 'Doğrulanmış Rozet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'discount', 'İndirim');
            $lang_update_queries[] = PT_UpdateLangs($value, 'max_upload', 'Maksimum Yükleme');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_download', 'Şarkıları İndir');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_station_import', 'İthalat İstasyonu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_sell', 'Şarkıları Sat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_multi_upload', 'Çoklu Şarkı Yükle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_channel_trailer', 'Kanal Fragmanı Yükle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_blog', 'Blog yarat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_upload', 'Şarkı Yükle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_soundcloud_import', 'Soundcloud&#39;u İçe Aktarma');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_deezer_import', 'Deezer İthalatı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_import', 'Itunes İthalatı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_affiliate', 'Itunes İş Ortağı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_youtube_import', 'Youtube İçe Aktarma');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_user_ads', 'Reklam Oluştur');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_audio_ads', 'Sesli Reklamlar Oluşturun');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_event_system', 'Etkinlik Oluştur');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_story_system', 'Hikaye Oluştur');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_store_system', 'Ürünleri sat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'after_artist', 'Hesabınız bir sanatçı olduğunda bu özellikleri kullanabilirsiniz.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'category_can_not_be_empty', 'Kategori boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pro_package', 'Profesyonel Paket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'select_pro_package', 'Pro Paketi Seçin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_point_system', 'Puan Kazanın');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_donate_system', 'Bağış Düğmesi');
        } else if ($value == 'english') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song_alert', 'At least one uploaded song is required to post a product, you can {LINK}.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song', 'upload a new song');
            $lang_update_queries[] = PT_UpdateLangs($value, 'send_again', 'Resend');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_two_expired', 'Code expired please try to login again');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_cant_send_now', 'Please wait a few seconds before requesting a new link.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_successfully_sent', 'Code sent successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'mark_all_as_read', 'Mark all as read');
            $lang_update_queries[] = PT_UpdateLangs($value, 'withdraw_method', 'Withdraw method');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank', 'Bank');
            $lang_update_queries[] = PT_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = PT_UpdateLangs($value, 'transfer_to', 'Transfer to');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iban', 'Iban');
            $lang_update_queries[] = PT_UpdateLangs($value, 'swift_code', 'Swift code');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_payment_method', 'Please select a payment method');
            $lang_update_queries[] = PT_UpdateLangs($value, 'week', 'Week');
            $lang_update_queries[] = PT_UpdateLangs($value, 'unlimited', 'Unlimited');
            $lang_update_queries[] = PT_UpdateLangs($value, 'featured_member', 'Featured Member');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_badge', 'Verified Badge');
            $lang_update_queries[] = PT_UpdateLangs($value, 'discount', 'Discount');
            $lang_update_queries[] = PT_UpdateLangs($value, 'max_upload', 'Max Upload');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_download', 'Download Songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_station_import', 'Import Station');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_sell', 'Sell Songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_multi_upload', 'Upload Multi Songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_channel_trailer', 'Upload Channel Trailer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_blog', 'Create Blog');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_upload', 'Upload Songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_soundcloud_import', 'Soundcloud Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_deezer_import', 'Deezer Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_import', 'Itunes Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_affiliate', 'Itunes Affiliate');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_youtube_import', 'Youtube Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_user_ads', 'Create Ads');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_audio_ads', 'Create Audio Ads');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_event_system', 'Create Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_story_system', 'Create Story');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_store_system', 'Sell Products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'after_artist', 'You can use those features once your account become an artist.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'category_can_not_be_empty', 'Category can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pro_package', 'Pro Package');
            $lang_update_queries[] = PT_UpdateLangs($value, 'select_pro_package', 'Select Pro Package');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_point_system', 'Earn Points');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_donate_system', 'Donate Button');
        } else if ($value != 'english') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song_alert', 'At least one uploaded song is required to post a product, you can {LINK}.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_a_new_song', 'upload a new song');
            $lang_update_queries[] = PT_UpdateLangs($value, 'send_again', 'Resend');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_two_expired', 'Code expired please try to login again');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_cant_send_now', 'Please wait a few seconds before requesting a new link.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'code_successfully_sent', 'Code sent successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'mark_all_as_read', 'Mark all as read');
            $lang_update_queries[] = PT_UpdateLangs($value, 'withdraw_method', 'Withdraw method');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank', 'Bank');
            $lang_update_queries[] = PT_UpdateLangs($value, 'skrill', 'Skrill');
            $lang_update_queries[] = PT_UpdateLangs($value, 'transfer_to', 'Transfer to');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iban', 'Iban');
            $lang_update_queries[] = PT_UpdateLangs($value, 'swift_code', 'Swift code');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_payment_method', 'Please select a payment method');
            $lang_update_queries[] = PT_UpdateLangs($value, 'week', 'Week');
            $lang_update_queries[] = PT_UpdateLangs($value, 'unlimited', 'Unlimited');
            $lang_update_queries[] = PT_UpdateLangs($value, 'featured_member', 'Featured Member');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_badge', 'Verified Badge');
            $lang_update_queries[] = PT_UpdateLangs($value, 'discount', 'Discount');
            $lang_update_queries[] = PT_UpdateLangs($value, 'max_upload', 'Max Upload');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_download', 'Download Songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_station_import', 'Import Station');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_sell', 'Sell Songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_multi_upload', 'Upload Multi Songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_channel_trailer', 'Upload Channel Trailer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_blog', 'Create Blog');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_upload', 'Upload Songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_soundcloud_import', 'Soundcloud Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_deezer_import', 'Deezer Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_import', 'Itunes Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_itunes_affiliate', 'Itunes Affiliate');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_youtube_import', 'Youtube Import');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_user_ads', 'Create Ads');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_audio_ads', 'Create Audio Ads');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_event_system', 'Create Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_story_system', 'Create Story');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_store_system', 'Sell Products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'after_artist', 'You can use those features once your account become an artist.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'category_can_not_be_empty', 'Category can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pro_package', 'Pro Package');
            $lang_update_queries[] = PT_UpdateLangs($value, 'select_pro_package', 'Select Pro Package');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_point_system', 'Earn Points');
            $lang_update_queries[] = PT_UpdateLangs($value, 'can_use_donate_system', 'Donate Button');
        }
    }
    if (!empty($lang_update_queries)) {
        foreach ($lang_update_queries as $key => $query) {
            $sql = mysqli_query($mysqli, $query);
        }
        $languages_list = array(
            array(
                "name" => "Afrikaans",
                "code" => "af"
            ),
            array(
                "name" => "Albanian - shqip",
                "code" => "sq"
            ),
            array(
                "name" => "Amharic - አማርኛ",
                "code" => "am"
            ),
            array(
                "name" => "Arabic - العربية",
                "code" => "ar"
            ),
            array(
                "name" => "Aragonese - aragonés",
                "code" => "an"
            ),
            array(
                "name" => "Armenian - հայերեն",
                "code" => "hy"
            ),
            array(
                "name" => "Asturian - asturianu",
                "code" => "ast"
            ),
            array(
                "name" => "Azerbaijani - azərbaycan dili",
                "code" => "az"
            ),
            array(
                "name" => "Basque - euskara",
                "code" => "eu"
            ),
            array(
                "name" => "Belarusian - беларуская",
                "code" => "be"
            ),
            array(
                "name" => "Bengali - বাংলা",
                "code" => "bn"
            ),
            array(
                "name" => "Bosnian - bosanski",
                "code" => "bs"
            ),
            array(
                "name" => "Breton - brezhoneg",
                "code" => "br"
            ),
            array(
                "name" => "Bulgarian - български",
                "code" => "bg"
            ),
            array(
                "name" => "Catalan - català",
                "code" => "ca"
            ),
            array(
                "name" => "Central Kurdish - کوردی (دەستنوسی عەرەبی)",
                "code" => "ckb"
            ),
            array(
                "name" => "Chinese - 中文",
                "code" => "zh"
            ),
            array(
                "name" => "Chinese (Hong Kong) - 中文（香港）",
                "code" => "zh-HK"
            ),
            array(
                "name" => "Chinese (Simplified) - 中文（简体）",
                "code" => "zh-CN"
            ),
            array(
                "name" => "Chinese (Traditional) - 中文（繁體）",
                "code" => "zh-TW"
            ),
            array(
                "name" => "Corsican",
                "code" => "co"
            ),
            array(
                "name" => "Croatian - hrvatski",
                "code" => "hr"
            ),
            array(
                "name" => "Czech - čeština",
                "code" => "cs"
            ),
            array(
                "name" => "Danish - dansk",
                "code" => "da"
            ),
            array(
                "name" => "Dutch - Nederlands",
                "code" => "nl"
            ),
            array(
                "name" => "English",
                "code" => "en"
            ),
            array(
                "name" => "English (Australia)",
                "code" => "en-AU"
            ),
            array(
                "name" => "English (Canada)",
                "code" => "en-CA"
            ),
            array(
                "name" => "English (India)",
                "code" => "en-IN"
            ),
            array(
                "name" => "English (New Zealand)",
                "code" => "en-NZ"
            ),
            array(
                "name" => "English (South Africa)",
                "code" => "en-ZA"
            ),
            array(
                "name" => "English (United Kingdom)",
                "code" => "en-GB"
            ),
            array(
                "name" => "English (United States)",
                "code" => "en-US"
            ),
            array(
                "name" => "Esperanto - esperanto",
                "code" => "eo"
            ),
            array(
                "name" => "Estonian - eesti",
                "code" => "et"
            ),
            array(
                "name" => "Faroese - føroyskt",
                "code" => "fo"
            ),
            array(
                "name" => "Filipino",
                "code" => "fil"
            ),
            array(
                "name" => "Finnish - suomi",
                "code" => "fi"
            ),
            array(
                "name" => "French - français",
                "code" => "fr"
            ),
            array(
                "name" => "French (Canada) - français (Canada)",
                "code" => "fr-CA"
            ),
            array(
                "name" => "French (France) - français (France)",
                "code" => "fr-FR"
            ),
            array(
                "name" => "French (Switzerland) - français (Suisse)",
                "code" => "fr-CH"
            ),
            array(
                "name" => "Galician - galego",
                "code" => "gl"
            ),
            array(
                "name" => "Georgian - ქართული",
                "code" => "ka"
            ),
            array(
                "name" => "German - Deutsch",
                "code" => "de"
            ),
            array(
                "name" => "German (Austria) - Deutsch (Österreich)",
                "code" => "de-AT"
            ),
            array(
                "name" => "German (Germany) - Deutsch (Deutschland)",
                "code" => "de-DE"
            ),
            array(
                "name" => "German (Liechtenstein) - Deutsch (Liechtenstein)",
                "code" => "de-LI"
            ),
            array(
                "name" => "German (Switzerland) - Deutsch (Schweiz)",
                "code" => "de-CH"
            ),
            array(
                "name" => "Greek - Ελληνικά",
                "code" => "el"
            ),
            array(
                "name" => "Guarani",
                "code" => "gn"
            ),
            array(
                "name" => "Gujarati - ગુજરાતી",
                "code" => "gu"
            ),
            array(
                "name" => "Hausa",
                "code" => "ha"
            ),
            array(
                "name" => "Hawaiian - ʻŌlelo Hawaiʻi",
                "code" => "haw"
            ),
            array(
                "name" => "Hebrew - עברית",
                "code" => "he"
            ),
            array(
                "name" => "Hindi - हिन्दी",
                "code" => "hi"
            ),
            array(
                "name" => "Hungarian - magyar",
                "code" => "hu"
            ),
            array(
                "name" => "Icelandic - íslenska",
                "code" => "is"
            ),
            array(
                "name" => "Indonesian - Indonesia",
                "code" => "id"
            ),
            array(
                "name" => "Interlingua",
                "code" => "ia"
            ),
            array(
                "name" => "Irish - Gaeilge",
                "code" => "ga"
            ),
            array(
                "name" => "Italian - italiano",
                "code" => "it"
            ),
            array(
                "name" => "Italian (Italy) - italiano (Italia)",
                "code" => "it-IT"
            ),
            array(
                "name" => "Italian (Switzerland) - italiano (Svizzera)",
                "code" => "it-CH"
            ),
            array(
                "name" => "Japanese - 日本語",
                "code" => "ja"
            ),
            array(
                "name" => "Kannada - ಕನ್ನಡ",
                "code" => "kn"
            ),
            array(
                "name" => "Kazakh - қазақ тілі",
                "code" => "kk"
            ),
            array(
                "name" => "Khmer - ខ្មែរ",
                "code" => "km"
            ),
            array(
                "name" => "Korean - 한국어",
                "code" => "ko"
            ),
            array(
                "name" => "Kurdish - Kurdî",
                "code" => "ku"
            ),
            array(
                "name" => "Kyrgyz - кыргызча",
                "code" => "ky"
            ),
            array(
                "name" => "Lao - ລາວ",
                "code" => "lo"
            ),
            array(
                "name" => "Latin",
                "code" => "la"
            ),
            array(
                "name" => "Latvian - latviešu",
                "code" => "lv"
            ),
            array(
                "name" => "Lingala - lingála",
                "code" => "ln"
            ),
            array(
                "name" => "Lithuanian - lietuvių",
                "code" => "lt"
            ),
            array(
                "name" => "Macedonian - македонски",
                "code" => "mk"
            ),
            array(
                "name" => "Malay - Bahasa Melayu",
                "code" => "ms"
            ),
            array(
                "name" => "Malayalam - മലയാളം",
                "code" => "ml"
            ),
            array(
                "name" => "Maltese - Malti",
                "code" => "mt"
            ),
            array(
                "name" => "Marathi - मराठी",
                "code" => "mr"
            ),
            array(
                "name" => "Mongolian - монгол",
                "code" => "mn"
            ),
            array(
                "name" => "Nepali - नेपाली",
                "code" => "ne"
            ),
            array(
                "name" => "Norwegian - norsk",
                "code" => "no"
            ),
            array(
                "name" => "Norwegian Bokmål - norsk bokmål",
                "code" => "nb"
            ),
            array(
                "name" => "Norwegian Nynorsk - nynorsk",
                "code" => "nn"
            ),
            array(
                "name" => "Occitan",
                "code" => "oc"
            ),
            array(
                "name" => "Oriya - ଓଡ଼ିଆ",
                "code" => "or"
            ),
            array(
                "name" => "Oromo - Oromoo",
                "code" => "om"
            ),
            array(
                "name" => "Pashto - پښتو",
                "code" => "ps"
            ),
            array(
                "name" => "Persian - فارسی",
                "code" => "fa"
            ),
            array(
                "name" => "Polish - polski",
                "code" => "pl"
            ),
            array(
                "name" => "Portuguese - português",
                "code" => "pt"
            ),
            array(
                "name" => "Portuguese (Brazil) - português (Brasil)",
                "code" => "pt-BR"
            ),
            array(
                "name" => "Portuguese (Portugal) - português (Portugal)",
                "code" => "pt-PT"
            ),
            array(
                "name" => "Punjabi - ਪੰਜਾਬੀ",
                "code" => "pa"
            ),
            array(
                "name" => "Quechua",
                "code" => "qu"
            ),
            array(
                "name" => "Romanian - română",
                "code" => "ro"
            ),
            array(
                "name" => "Romanian (Moldova) - română (Moldova)",
                "code" => "mo"
            ),
            array(
                "name" => "Romansh - rumantsch",
                "code" => "rm"
            ),
            array(
                "name" => "Russian - русский",
                "code" => "ru"
            ),
            array(
                "name" => "Scottish Gaelic",
                "code" => "gd"
            ),
            array(
                "name" => "Serbian - српски",
                "code" => "sr"
            ),
            array(
                "name" => "Serbo - Croatian",
                "code" => "sh"
            ),
            array(
                "name" => "Shona - chiShona",
                "code" => "sn"
            ),
            array(
                "name" => "Sindhi",
                "code" => "sd"
            ),
            array(
                "name" => "Sinhala - සිංහල",
                "code" => "si"
            ),
            array(
                "name" => "Slovak - slovenčina",
                "code" => "sk"
            ),
            array(
                "name" => "Slovenian - slovenščina",
                "code" => "sl"
            ),
            array(
                "name" => "Somali - Soomaali",
                "code" => "so"
            ),
            array(
                "name" => "Southern Sotho",
                "code" => "st"
            ),
            array(
                "name" => "Spanish - español",
                "code" => "es"
            ),
            array(
                "name" => "Spanish (Argentina) - español (Argentina)",
                "code" => "es-AR"
            ),
            array(
                "name" => "Spanish (Latin America) - español (Latinoamérica)",
                "code" => "es-419"
            ),
            array(
                "name" => "Spanish (Mexico) - español (México)",
                "code" => "es-MX"
            ),
            array(
                "name" => "Spanish (Spain) - español (España)",
                "code" => "es-ES"
            ),
            array(
                "name" => "Spanish (United States) - español (Estados Unidos)",
                "code" => "es-US"
            ),
            array(
                "name" => "Sundanese",
                "code" => "su"
            ),
            array(
                "name" => "Swahili - Kiswahili",
                "code" => "sw"
            ),
            array(
                "name" => "Swedish - svenska",
                "code" => "sv"
            ),
            array(
                "name" => "Tajik - тоҷикӣ",
                "code" => "tg"
            ),
            array(
                "name" => "Tamil - தமிழ்",
                "code" => "ta"
            ),
            array(
                "name" => "Tatar",
                "code" => "tt"
            ),
            array(
                "name" => "Telugu - తెలుగు",
                "code" => "te"
            ),
            array(
                "name" => "Thai - ไทย",
                "code" => "th"
            ),
            array(
                "name" => "Tigrinya - ትግርኛ",
                "code" => "ti"
            ),
            array(
                "name" => "Tongan - lea fakatonga",
                "code" => "to"
            ),
            array(
                "name" => "Turkish - Türkçe",
                "code" => "tr"
            ),
            array(
                "name" => "Turkmen",
                "code" => "tk"
            ),
            array(
                "name" => "Twi",
                "code" => "tw"
            ),
            array(
                "name" => "Ukrainian - українська",
                "code" => "uk"
            ),
            array(
                "name" => "Urdu - اردو",
                "code" => "ur"
            ),
            array(
                "name" => "Uyghur",
                "code" => "ug"
            ),
            array(
                "name" => "Uzbek - o‘zbek",
                "code" => "uz"
            ),
            array(
                "name" => "Vietnamese - Tiếng Việt",
                "code" => "vi"
            ),
            array(
                "name" => "Walloon - wa",
                "code" => "wa"
            ),
            array(
                "name" => "Welsh - Cymraeg",
                "code" => "cy"
            ),
            array(
                "name" => "Western Frisian",
                "code" => "fy"
            ),
            array(
                "name" => "Xhosa",
                "code" => "xh"
            ),
            array(
                "name" => "Yiddish",
                "code" => "yi"
            ),
            array(
                "name" => "Yoruba - Èdè Yorùbá",
                "code" => "yo"
            ),
            array(
                "name" => "Zulu - isiZulu",
                "code" => "zu"
            )
        );
        $a              = array();
        foreach ($languages_list as $key => $value) {
            $pieces = explode("-", $value['name']);
            if (!empty($pieces)) {
                foreach ($pieces as $key2 => $value2) {
                    $a[trim(strtolower($value2))] = trim(strtolower($value['code']));
                }
            }
        }
        foreach ($music->langs as $key => $value) {
            if (in_array($value, array_keys($a))) {
                $count = $db->where('lang_name', $value)->getValue(T_LANG_ISO, 'COUNT(*)');
                if ($count > 0) {
                    $db->where('lang_name', $value)->update(T_LANG_ISO, array(
                        'iso' => $a[$value]
                    ));
                } else {
                    $db->insert(T_LANG_ISO, array(
                        'lang_name' => $value,
                        'iso' => $a[$value]
                    ));
                }
            }
        }
        $files = array(
            'assets/import',
            'xhr/payu.php',
            'xhr/new-stripe-pro.php',
            'xhr/new-stripe.php',
            'xhr/stripe-pro.php',
            'xhr/stripe-wallet.php',
            'xhr/stripe.php',
            'xhr/get-pro-url.php',
            'xhr/purchase.php',
            'xhr/purchase-album.php',
            'xhr/purchase.php',
            'xhr/get-pro-url.php',
            'sources/stripe-purchase-pro.php',
            'sources/stripe-purchase-track.php',
            'sources/purchase.php',
            'sources/purchase-album.php',
            'sources/pro-purchase.php',
            'themes/default/layout/modals/paymentpro-modal.html',
            'themes/default/layout/modals/payment-modal.html',
            'themes/default/layout/modals/buy_album.html',
            'themes/volcano/layout/modals/paymentpro-modal.html',
            'themes/volcano/layout/modals/payment-modal.html',
            'themes/volcano/layout/modals/buy_album.html'
        );
        foreach ($files as $key => $value) {
            if (file_exists($value)) {
                if (is_dir($value)) {
                    deleteDirectory($value);
                } else {
                    @unlink($value);
                }
            }
        }
    }
    $name = md5(microtime()) . '_updated.php';
    rename('update.php', $name);
}
function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }
    if (!is_dir($dir)) {
        return unlink($dir);
    }
    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }
        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }
    return rmdir($dir);
}
?>
<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
      <meta name="viewport" content="width=device-width, initial-scale=1"/>
      <title>Updating DeepSound</title>
      <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
      <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
      <style>
         @import url('https://fonts.googleapis.com/css?family=Roboto:400,500');
         @media print {
            .wo_update_changelog {max-height: none !important; min-height: !important}
            .btn, .hide_print, .setting-well h4 {display:none;}
         }
         * {outline: none !important;}
         body {background: #f3f3f3;font-family: 'Roboto', sans-serif;}
         .light {font-weight: 400;}
         .bold {font-weight: 500;}
         .btn {height: 52px;line-height: 1;font-size: 16px;transition: all 0.3s;border-radius: 2em;font-weight: 500;padding: 0 28px;letter-spacing: .5px;}
         .btn svg {margin-left: 10px;margin-top: -2px;transition: all 0.3s;vertical-align: middle;}
         .btn:hover svg {-webkit-transform: translateX(3px);-moz-transform: translateX(3px);-ms-transform: translateX(3px);-o-transform: translateX(3px);transform: translateX(3px);}
         .btn-main {color: #ffffff;background-color: #f98f1d;border-color: #f98f1d;}
         .btn-main:disabled, .btn-main:focus {color: #fff;}
         .btn-main:hover {color: #ffffff;background-color: #0dcde2;border-color: #0dcde2;box-shadow: -2px 2px 14px rgba(168, 72, 73, 0.35);}
         svg {vertical-align: middle;}
         .main {color: #f98f1d;}
         .wo_update_changelog {
          border: 1px solid #eee;
          padding: 10px !important;
         }
         .content-container {display: -webkit-box; width: 100%;display: -moz-box;display: -ms-flexbox;display: -webkit-flex;display: flex;-webkit-flex-direction: column;flex-direction: column;min-height: 100vh;position: relative;}
         .content-container:before, .content-container:after {-webkit-box-flex: 1;box-flex: 1;-webkit-flex-grow: 1;flex-grow: 1;content: '';display: block;height: 50px;}
         .wo_install_wiz {position: relative;background-color: white;box-shadow: 0 1px 15px 2px rgba(0, 0, 0, 0.1);border-radius: 10px;padding: 20px 30px;border-top: 1px solid rgba(0, 0, 0, 0.04);}
         .wo_install_wiz h2 {margin-top: 10px;margin-bottom: 30px;display: flex;align-items: center;}
         .wo_install_wiz h2 span {margin-left: auto;font-size: 15px;}
         .wo_update_changelog {padding:0;list-style-type: none;margin-bottom: 15px;max-height: 440px;overflow-y: auto; min-height: 440px;}
         .wo_update_changelog li {margin-bottom:7px; max-height: 20px; overflow: hidden;}
         .wo_update_changelog li span {padding: 2px 7px;font-size: 12px;margin-right: 4px;border-radius: 2px;}
         .wo_update_changelog li span.added {background-color: #4CAF50;color: white;}
         .wo_update_changelog li span.changed {background-color: #e62117;color: white;}
         .wo_update_changelog li span.improved {background-color: #9C27B0;color: white;}
         .wo_update_changelog li span.compressed {background-color: #795548;color: white;}
         .wo_update_changelog li span.fixed {background-color: #2196F3;color: white;}
         input.form-control {background-color: #f4f4f4;border: 0;border-radius: 2em;height: 40px;padding: 3px 14px;color: #383838;transition: all 0.2s;}
input.form-control:hover {background-color: #e9e9e9;}
input.form-control:focus {background: #fff;box-shadow: 0 0 0 1.5px #a84849;}
         .empty_state {margin-top: 80px;margin-bottom: 80px;font-weight: 500;color: #6d6d6d;display: block;text-align: center;}
         .checkmark__circle {stroke-dasharray: 166;stroke-dashoffset: 166;stroke-width: 2;stroke-miterlimit: 10;stroke: #7ac142;fill: none;animation: stroke 0.6s cubic-bezier(0.65, 0, 0.45, 1) forwards;}
         .checkmark {width: 80px;height: 80px; border-radius: 50%;display: block;stroke-width: 3;stroke: #fff;stroke-miterlimit: 10;margin: 100px auto 50px;box-shadow: inset 0px 0px 0px #7ac142;animation: fill .4s ease-in-out .4s forwards, scale .3s ease-in-out .9s both;}
         .checkmark__check {transform-origin: 50% 50%;stroke-dasharray: 48;stroke-dashoffset: 48;animation: stroke 0.3s cubic-bezier(0.65, 0, 0.45, 1) 0.8s forwards;}
         @keyframes stroke { 100% {stroke-dashoffset: 0;}}
         @keyframes scale {0%, 100% {transform: none;}  50% {transform: scale3d(1.1, 1.1, 1); }}
         @keyframes fill { 100% {box-shadow: inset 0px 0px 0px 54px #7ac142; }}
      </style>
   </head>
   <body>
      <div class="content-container container">
         <div class="row">
            <div class="col-md-1"></div>
            <div class="col-md-10">
               <div class="wo_install_wiz">
                 <?php if ($updated == false) { ?>
                  <div>
                     <h2 class="light">Update to v1.4.7 </span></h2>
                     <div class="alert alert-danger">
                       <strong>Important:</strong> Don't run the update process before all the files were uploaded to your server, please make sure all files are uploaded to your server then click the update button below.
                     </div>
                     <div class="setting-well">
                        <h4>Changelog</h4>
                        <ul class="wo_update_changelog">
                          <li>[Added] user roles, editor, moderator, and admin.</li>
                          <li>[Added] the ability to add, edit and remove pro packages. </li>
                          <li>[Added] the ability to add custom withdrawal methods.</li>
                          <li>[Added] feature privacy, choose who can each user a feature, all, pro, admin, users or artists.</li>
                          <li>[Added] the ability to mark all messages as read.</li>
                          <li>[Added] time to messages.</li>
                          <li>[Added] the ability to resend email verfication and two auth emails.</li>
                          <li>[Added] hreflang tags.</li>
                          <li>[Improved] SEO of whole website.</li>
                          <li>[Improved] speed bt 50% more.</li>
                          <li>[Updated] all PHP libs to new versions.</li>
                          <li>[Fixed] earnings count was wrong.</li>
                          <li>[Fixed] verification badgfe now showing everywhere.</li>
                          <li>[Fixed] empty album showing when songs are private.</li>
                          <li>[Fixed] songs were downloaded as .html file if remote storage like Google cloud is enabled. </li>
                          <li>[Fixed] scrolling issue on admin panel.</li>
                          <li>[Fixed] 5 minor bugs.</li>
                        </ul>
                        <p class="hide_print">Note: The update process might take few minutes.</p>
                        <p class="hide_print">Important: If you got any fail queries, please copy them, open a support ticket and send us the details.</p>
                        <br>
                             <button class="pull-right btn btn-default" onclick="window.print();">Share Log</button>
                             <button type="button" class="btn btn-main" id="button-update">
                             Update
                             <svg viewBox="0 0 19 14" xmlns="http://www.w3.org/2000/svg" width="18" height="18">
                                <path fill="currentColor" d="M18.6 6.9v-.5l-6-6c-.3-.3-.9-.3-1.2 0-.3.3-.3.9 0 1.2l5 5H1c-.5 0-.9.4-.9.9s.4.8.9.8h14.4l-4 4.1c-.3.3-.3.9 0 1.2.2.2.4.2.6.2.2 0 .4-.1.6-.2l5.2-5.2h.2c.5 0 .8-.4.8-.8 0-.3 0-.5-.2-.7z"></path>
                             </svg>
                          </button>
                     </div>
                     <?php }?>
                     <?php if ($updated == true) { ?>
                      <div>
                        <div class="empty_state">
                           <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                              <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                              <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                           </svg>
                           <p>Congratulations, you have successfully updated your site. Thanks for choosing DeepSound.</p>
                           <br>
                           <a href="<?php echo $wo['config']['site_url'] ?>" class="btn btn-main" style="line-height:50px;">Home</a>
                        </div>
                     </div>
                     <?php }?>
                  </div>
               </div>
            </div>
            <div class="col-md-1"></div>
         </div>
      </div>
   </body>
</html>
<script>
var queries = [
    "UPDATE `config` SET value = '1.4.7' WHERE name = 'version';",
    "ALTER TABLE `users` ADD `permission` VARCHAR(3000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '{\"add-categories\":0,\"add-language\":0,\"add-new-article\":0,\"add-new-custom-page\":0,\"add-new-profile-field\":0,\"ads-settings\":0,\"affiliates-settings\":0,\"api-settings\":0,\"auto-friend\":0,\"backup\":0,\"ban-users\":0,\"bank-receipts\":0,\"change-site-desgin\":0,\"changelog\":0,\"changelogs.html\":0,\"create-new-sitemap\":0,\"custom-design\":0,\"dashboard\":0,\"edit-article\":0,\"edit-categories\":0,\"edit-custom-page\":0,\"edit-lang\":0,\"edit-profile-field\":0,\"edit-question\":0,\"edit-terms-pages\":0,\"email-settings\":0,\"events-settings\":0,\"fake-users\":0,\"general-settings\":0,\"import-settings\":0,\"js\":0,\"mailing-list\":0,\"manage-albums\":0,\"manage-announcements\":0,\"manage-answers\":0,\"manage-articles\":0,\"manage-artists\":0,\"manage-blog-categories\":0,\"manage-categories\":0,\"manage-copyright-reports\":0,\"manage-currencies\":0,\"manage-custom-pages\":0,\"manage-events\":0,\"manage-faqs\":0,\"manage-invitation\":0,\"manage-invitation-keys\":0,\"manage-languages\":0,\"manage-orders\":0,\"manage-pages\":0,\"manage-payments\":0,\"manage-permission\":0,\"manage-playlist\":0,\"manage-products\":0,\"manage-profile-fields\":0,\"manage-questions\":0,\"manage-refund\":0,\"manage-reply\":0,\"manage-reports\":0,\"manage-reviews\":0,\"manage-song-price\":0,\"manage-songs\":0,\"manage-stories\":0,\"manage-themes\":0,\"manage-track-reviews\":0,\"manage-user-ads\":0,\"manage-users\":0,\"manage-website-ads\":0,\"manage_emails\":0,\"mass-notifications\":0,\"payment-requests\":0,\"payment-settings\":0,\"payments\":0,\"pro-memebers\":0,\"pro-payments\":0,\"pro-settings\":0,\"products-categories\":0,\"push-notifications-system\":0,\"s3\":0,\"send_email\":0,\"seo\":0,\"site-settings\":0,\"sms_settings\":0,\"social-login\":0,\"store-settings\":0,\"story-settings\":0}' AFTER `coinpayments_txn_id`, ADD INDEX (`permission`);",
    "ALTER TABLE `users` ADD `two_factor_hash` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `two_factor`, ADD INDEX (`two_factor_hash`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'withdrawal_payment_method', '{\"paypal\":1,\"bank\":0,\"skrill\":0,\"custom\":0}');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'custom_name', '');",
    "ALTER TABLE `withdrawal_requests` ADD `transfer_info` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `status`, ADD INDEX (`transfer_info`);",
    "ALTER TABLE `withdrawal_requests` ADD `iban` VARCHAR(250) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `transfer_info`, ADD `country` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `iban`, ADD `full_name` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `country`, ADD `swift_code` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `full_name`, ADD `address` VARCHAR(600) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `swift_code`, ADD `type` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `address`, ADD INDEX (`iban`), ADD INDEX (`country`), ADD INDEX (`full_name`), ADD INDEX (`swift_code`), ADD INDEX (`address`), ADD INDEX (`type`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_sell', 'artist');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_multi_upload', 'all');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_blog', 'all');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_station_import', 'all');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_soundcloud_import', 'all');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_deezer_import', 'all');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_itunes_import', 'all');",
    "UPDATE `config` SET `value`='admin' WHERE `name`='itunes_affiliate';",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_youtube_import', 'all');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_user_ads', 'all');",
    "UPDATE `config` SET `value`='all' WHERE `name`='who_audio_ads';",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_event_system', 'artist');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_story_system', 'all');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_store_system', 'artist');",
    "CREATE TABLE `manage_pro` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `type` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `price` FLOAT(11) NOT NULL DEFAULT '0' , `featured_member` INT(2) NOT NULL DEFAULT '0' , `verified_badge` INT(2) NOT NULL DEFAULT '0' , `artist_member` INT(2) NOT NULL DEFAULT '0' , `discount` INT(11) NOT NULL DEFAULT '0' , `image` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `night_image` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `color` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '#fafafa' , `description` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `status` INT(2) NOT NULL DEFAULT '1' , `time` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'month' , `time_count` INT(11) NOT NULL DEFAULT '1' , `max_upload` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '96000000' , `features` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '{\"can_use_download\":1,\"can_use_sell\":1,\"can_use_multi_upload\":1,\"can_use_channel_trailer\":1,\"can_use_blog\":1,\"can_use_station_import\":1,\"can_use_upload\":1,\"can_use_soundcloud_import\":1,\"can_use_deezer_import\":1,\"can_use_itunes_import\":1,\"can_use_itunes_affiliate\":1,\"can_use_youtube_import\":1,\"can_use_user_ads\":1,\"can_use_audio_ads\":1,\"can_use_event_system\":1,\"can_use_story_system\":1,\"can_use_store_system\":1}' , PRIMARY KEY (`id`), INDEX (`type`), INDEX (`price`), INDEX (`featured_member`), INDEX (`verified_badge`), INDEX (`artist_member`), INDEX (`discount`), INDEX (`image`), INDEX (`night_image`), INDEX (`color`), INDEX (`status`), INDEX (`time`), INDEX (`time_count`), INDEX (`max_upload`), INDEX (`features`)) ENGINE = InnoDB;",
    "INSERT INTO `manage_pro` (`id`, `type`, `price`, `featured_member`, `verified_badge`, `artist_member`, `discount`, `image`, `night_image`, `color`, `description`, `status`, `time`, `time_count`, `max_upload`, `features`) VALUES (NULL, 'pro', '9', '1', '1', '0', '0', '', '', '#f98f1d', '', '1', 'month', '1', '96000000', '{\"can_use_download\":1,\"can_use_sell\":1,\"can_use_multi_upload\":1,\"can_use_channel_trailer\":1,\"can_use_blog\":1,\"can_use_station_import\":1,\"can_use_upload\":1,\"can_use_soundcloud_import\":1,\"can_use_deezer_import\":1,\"can_use_itunes_import\":1,\"can_use_itunes_affiliate\":1,\"can_use_youtube_import\":1,\"can_use_user_ads\":1,\"can_use_audio_ads\":1,\"can_use_event_system\":1,\"can_use_story_system\":1,\"can_use_store_system\":1}');",
    "ALTER TABLE `users` ADD `pro_type` INT(3) NOT NULL DEFAULT '0' AFTER `is_pro`, ADD INDEX (`pro_type`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_point_system', 'all');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'who_can_donate_system', 'all');",
    "ALTER TABLE `manage_pro` CHANGE `features` `features` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '{\"can_use_download\":1,\"can_use_sell\":1,\"can_use_multi_upload\":1,\"can_use_channel_trailer\":1,\"can_use_blog\":1,\"can_use_station_import\":1,\"can_use_upload\":1,\"can_use_soundcloud_import\":1,\"can_use_deezer_import\":1,\"can_use_itunes_import\":1,\"can_use_itunes_affiliate\":1,\"can_use_youtube_import\":1,\"can_use_user_ads\":1,\"can_use_audio_ads\":1,\"can_use_event_system\":1,\"can_use_story_system\":1,\"can_use_store_system\":1,\"can_use_point_system\":1,\"can_use_donate_system\":1}';",
    "UPDATE `manage_pro` SET `features` = '{\"can_use_download\":1,\"can_use_sell\":1,\"can_use_multi_upload\":1,\"can_use_channel_trailer\":1,\"can_use_blog\":1,\"can_use_station_import\":1,\"can_use_upload\":1,\"can_use_soundcloud_import\":1,\"can_use_deezer_import\":1,\"can_use_itunes_import\":1,\"can_use_itunes_affiliate\":1,\"can_use_youtube_import\":1,\"can_use_user_ads\":1,\"can_use_audio_ads\":1,\"can_use_event_system\":1,\"can_use_story_system\":1,\"can_use_store_system\":1,\"can_use_point_system\":1,\"can_use_donate_system\":1}' WHERE `manage_pro`.`id` > 0;",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'developer_mode', 'off');",
    "ALTER TABLE `users`  DROP `paystack_ref`,  DROP `ConversationId`,  DROP `securionpay_key`,  DROP `cashfree_key`,  DROP `yoomoney_hash`,  DROP `fortumo_hash`,  DROP `aamarpay_tran_id`,  DROP `ngenius_ref`,  DROP `coinbase_hash`,  DROP `coinpayments_txn_id`,  DROP `coinbase_code`;",
    "CREATE TABLE `pending_payments` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `payment_data` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `method_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`payment_data`), INDEX (`method_name`), INDEX (`time`)) ENGINE = InnoDB;",
    "CREATE TABLE `lang_iso` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `lang_name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `iso` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `image` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , PRIMARY KEY (`id`), INDEX (`lang_name`), INDEX (`iso`), INDEX (`image`)) ENGINE = InnoDB;",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'upload_a_new_song_alert');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'upload_a_new_song');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'send_again');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'code_two_expired');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'you_cant_send_now');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'code_successfully_sent');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'mark_all_as_read');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'withdraw_method');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'bank');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'skrill');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'transfer_to');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'iban');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'swift_code');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'please_select_payment_method');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'week');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'unlimited');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'featured_member');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'verified_badge');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'discount');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'max_upload');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_download');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_station_import');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_sell');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_multi_upload');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_channel_trailer');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_blog');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_upload');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_soundcloud_import');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_deezer_import');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_itunes_import');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_itunes_affiliate');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_youtube_import');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_user_ads');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_audio_ads');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_event_system');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_story_system');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_store_system');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'after_artist');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'category_can_not_be_empty');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'pro_package');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'select_pro_package');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_point_system');",
  "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'can_use_donate_system');",
];

$('#input_code').bind("paste keyup input propertychange", function(e) {
    if (isPurchaseCode($(this).val())) {
        $('#button-update').removeAttr('disabled');
    } else {
        $('#button-update').attr('disabled', 'true');
    }
});

function isPurchaseCode(str) {
    var patt = new RegExp("(.*)-(.*)-(.*)-(.*)-(.*)");
    var res = patt.test(str);
    if (res) {
        return true;
    }
    return false;
}

$(document).on('click', '#button-update', function(event) {
    if ($('body').attr('data-update') == 'true') {
        window.location.href = '<?php echo $site_url?>';
        return false;
    }
    $(this).attr('disabled', true);
    $('.wo_update_changelog').html('');
    $('.wo_update_changelog').css({
        background: '#1e2321',
        color: '#fff'
    });
    $('.setting-well h4').text('Updating..');
    $(this).attr('disabled', true);
    RunQuery();
});

var queriesLength = queries.length;
var query = queries[0];
var count = 0;
function b64EncodeUnicode(str) {
    // first we use encodeURIComponent to get percent-encoded UTF-8,
    // then we convert the percent encodings into raw bytes which
    // can be fed into btoa.
    return btoa(encodeURIComponent(str).replace(/%([0-9A-F]{2})/g,
        function toSolidBytes(match, p1) {
            return String.fromCharCode('0x' + p1);
    }));
}
function RunQuery() {
    var query = queries[count];
    $.post('?update', {
        query: b64EncodeUnicode(query)
    }, function(data, textStatus, xhr) {
        if (data.status == 200) {
            $('.wo_update_changelog').append('<li><span class="added">SUCCESS</span> ~$ mysql > ' + query + '</li>');
        } else {
            $('.wo_update_changelog').append('<li><span class="changed">FAILED</span> ~$ mysql > ' + query + '</li>');
        }
        count = count + 1;
        if (queriesLength > count) {
            setTimeout(function() {
                RunQuery();
            }, 1500);
        } else {
            $('.wo_update_changelog').append('<li><span class="added">Updating Langauges & Categories</span> ~$ languages.sh, Please wait, this might take some time..</li>');
            $.post('?run_lang', {
                update_langs: 'true'
            }, function(data, textStatus, xhr) {
              $('.wo_update_changelog').append('<li><span class="fixed">Finished!</span> ~$ Congratulations! you have successfully updated your site. Thanks for choosing DeepSound.</li>');
              $('.setting-well h4').text('Update Log');
              $('#button-update').html('Home <svg viewBox="0 0 19 14" xmlns="http://www.w3.org/2000/svg" width="18" height="18"> <path fill="currentColor" d="M18.6 6.9v-.5l-6-6c-.3-.3-.9-.3-1.2 0-.3.3-.3.9 0 1.2l5 5H1c-.5 0-.9.4-.9.9s.4.8.9.8h14.4l-4 4.1c-.3.3-.3.9 0 1.2.2.2.4.2.6.2.2 0 .4-.1.6-.2l5.2-5.2h.2c.5 0 .8-.4.8-.8 0-.3 0-.5-.2-.7z"></path> </svg>');
              $('#button-update').attr('disabled', false);
              $(".wo_update_changelog").scrollTop($(".wo_update_changelog")[0].scrollHeight);
              $('body').attr('data-update', 'true');
            });
        }
        $(".wo_update_changelog").scrollTop($(".wo_update_changelog")[0].scrollHeight);
    });
}
</script>
