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
            $lang_update_queries[] = PT_UpdateLangs($value, 'playlist_not_found', 'قائمة التشغيل لم يتم العثور عليها');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_songs_sales', 'إجمالي الأغاني المباعة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_tickets_sales', 'إجمالي مبيعات التذاكر');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products_sales', 'إجمالي المنتجات المباعة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'faqs', 'الأسئلة الشائعة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'yoomoney', 'yoomoney');
            $lang_update_queries[] = PT_UpdateLangs($value, 'empty_amount', 'لا يمكن أن يكون المبلغ فارغًا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'fortumo', 'فورتومو');
            $lang_update_queries[] = PT_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ngenius', 'Ngenius');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinbase', 'Coinbase');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments', 'المدفوعات Coinpays');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_approved', 'تمت الموافقة على الدفع الخاص بك باستخدام المدفوعات coinpays');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_canceled', 'تم إلغاء الدفع الخاص بك باستخدام المدفوعات coinpays');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remember_device', 'تذكر هذا الجهاز');
            $lang_update_queries[] = PT_UpdateLangs($value, 'least_characters', 'يجب ألا يقل طول الأحرف عن 6 أحرف.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_lowercase', 'يجب أن تحتوي على حرف صغير.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_uppercase', 'يجب أن تحتوي على حرف كبير.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'number_special', 'يجب أن تحتوي على رقم أو شخصية خاصة.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'complexity_requirements', 'لا تلبي كلمة المرور المقدمة الحد الأدنى من متطلبات التعقيد');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name', 'الاسم الاول');
            $lang_update_queries[] = PT_UpdateLangs($value, 'last_name', 'الكنية');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name_last_name_empty', 'اسمك الأول واسم العائلة لا يمكن أن يكون فارغًا.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'terms_of_use_page', '<h4> 1- اكتب شروط الاستخدام هنا. </h4> <br> <p> </p> <br> <p> lorem ipsum dolor sit amet ، exectetur adisdpising elit ، sed do eiusmod incididunt ut labore et ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'about_page', '] ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'privacy_policy_page', '<h4> 1- اكتب سياسة الخصوصية الخاصة بك هنا. </h4> <br> <p> </p> <br> <p> lorem ipsum dolor sit amet ، exectetur adisdpising elit ، sed do eiusmod incididunt ut labore et dolore ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'dmca_terms_page', '<h4> 1- اكتب إشعار DMCA الخاص بك. </h4> <br> <p> </p> <br> <p> lorem ipsum dolor sit amet ، exectetur adisdpising elit ، sed do eiusmod regiDunt ut labore et dolore magna ');
        } else if ($value == 'dutch') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'playlist_not_found', 'Afspeellijst niet gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_songs_sales', 'Totaal verkochte nummers');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_tickets_sales', 'Totale tickets verkopen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products_sales', 'Totaal verkochte producten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'faqs', 'FAQ\'s');
            $lang_update_queries[] = PT_UpdateLangs($value, 'yoomoney', 'Yoomoney');
            $lang_update_queries[] = PT_UpdateLangs($value, 'empty_amount', 'Bedrag kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ngenius', 'NGENIUS');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinbase', 'Coinbase');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments', 'Munten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_approved', 'Uw betaling met behulp van CoinPayments is goedgekeurd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_canceled', 'Uw betaling met behulp van CoinPayments is geannuleerd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remember_device', 'Onthoud dit apparaat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'least_characters', 'Moet minimaal 6 tekens lang zijn.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_lowercase', 'Moet een kleine letter bevatten.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_uppercase', 'Moet een hoofdletter bevatten.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'number_special', 'Moet een nummer of speciaal teken bevatten.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'complexity_requirements', 'Het geleverde wachtwoord voldoet niet aan de minimale complexiteitsvereisten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name', 'Voornaam');
            $lang_update_queries[] = PT_UpdateLangs($value, 'last_name', 'Achternaam');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name_last_name_empty', 'Uw voornaam en achternaam kunnen niet leeg zijn.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'terms_of_use_page', '<H4> 1- Schrijf hier uw gebruiksvoorwaarden. </h4> <br> <p> </p> <br> <p> Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod Temporidunt ut labore et ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'about_page', '<H4> 1- Schrijf hier over ons. </h4> <br> <p> </p> <br> <p> Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod temporididunt ut labore et dolore ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'privacy_policy_page', '<H4> 1- Schrijf hier uw privacybeleid. </h4> <br> <p> </p> <br> <p> Lorem ipsum dolor sit amet, Consectetur adisdpisicing elit, sed do eiusmod Temporidunt ut labore et dolore et dolore ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'dmca_terms_page', '<H4> 1- Schrijf uw DMCA-kennisgeving. </h4> <br> <p> </p> <br> <p> Lorem ipsum dolor sit amet, Consectetur adisdpisicing elit, sed do eiusmod temporidInt ut labore et dolore magna ');
        } else if ($value == 'french') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'playlist_not_found', 'Liste de lecture introuvable');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_songs_sales', 'Total des chansons vendues');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_tickets_sales', 'Ventes totales de billets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products_sales', 'Produits totaux vendus');
            $lang_update_queries[] = PT_UpdateLangs($value, 'faqs', 'FAQ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'yoomoney', 'Joom');
            $lang_update_queries[] = PT_UpdateLangs($value, 'empty_amount', 'Le montant ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ngenius', 'NGENIUS');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinbase', 'Coincement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments', 'Paiement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_approved', 'Votre paiement à l\'aide de CoinPayments a été approuvé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_canceled', 'Votre paiement à l\'aide de CoinPayments a été annulé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remember_device', 'N\'oubliez pas cet appareil');
            $lang_update_queries[] = PT_UpdateLangs($value, 'least_characters', 'Doit contenir au moins 6 caractères.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_lowercase', 'Doit contenir une lettre minuscule.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_uppercase', 'Doit contenir une lettre majuscule.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'number_special', 'Doit contenir un numéro ou un caractère spécial.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'complexity_requirements', 'Le mot de passe fourni ne répond pas aux exigences de complexité minimale');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name', 'Prénom');
            $lang_update_queries[] = PT_UpdateLangs($value, 'last_name', 'Nom de famille');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name_last_name_empty', 'Votre prénom et votre nom de famille ne peuvent pas être vides.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'terms_of_use_page', '<h4> 1- Écrivez vos conditions d\'utilisation ici. </h4> <br> <p> </p> <br> <p> Lorem ipsum Dolor Sit Amet, Consectetur AdisdPising elit, sed do eiusmod tempory incidint ut Labore et ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'about_page', '<h4> 1- Écrivez-vous sur nous ici. </h4> <br> <p> </p> <br> <p> Lorem ipsum Dolor Sit Amet, Consectetur AdisdPising elit, sed do eiusmod tempory incidid ut Labore et Dolore ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'privacy_policy_page', '<h4> 1- Écrivez votre politique de confidentialité ici. </h4> <br> <p> </p> <br> <p> Lorem ipsum Dolor Sit Amet, Consectetur AdisdPising elit, sed do eiusmod tempory incidint ut Labore et Dolore ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'dmca_terms_page', '<h4> 1- Écrivez votre avis DMCA. </h4> <br> <p> </p> <br> <p> Lorem ipsum Dolor Sit Amet, Consectetur AdisdPising elit, sed do eiusmod tempory incidint ut Labore et Dolore Magna ');
        } else if ($value == 'german') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'playlist_not_found', 'Wiedergabeliste nicht gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_songs_sales', 'Gesamtlieder verkauft');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_tickets_sales', 'Gesamtkartenverkäufe');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products_sales', 'Gesamtprodukte verkauft');
            $lang_update_queries[] = PT_UpdateLangs($value, 'faqs', 'FAQs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'yoomoney', 'Yoomoney');
            $lang_update_queries[] = PT_UpdateLangs($value, 'empty_amount', 'Menge kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ngenius', 'Ngenius');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinbase', 'Coinbase');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments', 'Münzen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_approved', 'Ihre Zahlung mit Coinpayments wurde genehmigt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_canceled', 'Ihre Zahlung mit Coinpayments wurde storniert');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remember_device', 'erinnern Sie sich an dieses Gerät');
            $lang_update_queries[] = PT_UpdateLangs($value, 'least_characters', 'Muss mindestens 6 Zeichen lang sein.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_lowercase', 'Muss einen Kleinbuchstaben enthalten.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_uppercase', 'Muss einen Großbuchstaben enthalten.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'number_special', 'Muss eine Nummer oder ein spezielles Zeichen enthalten.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'complexity_requirements', 'Das gelieferte Passwort entspricht nicht den Anforderungen an die Mindestkomplexität');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name', 'Vorname');
            $lang_update_queries[] = PT_UpdateLangs($value, 'last_name', 'Nachname');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name_last_name_empty', 'Ihr Vorname und Ihr Nachname können nicht leer sein.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'terms_of_use_page', '<h4> 1- Schreiben Sie hier Ihre Nutzungsbedingungen. ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'about_page', '<h4> 1- Schreiben Sie hier über uns. ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'privacy_policy_page', '<h4> 1- Schreiben Sie hier Ihre Datenschutzrichtlinie. ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'dmca_terms_page', '<h4> 1- Schreiben Sie Ihren DMCA-Hinweis. ');
        } else if ($value == 'russian') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'playlist_not_found', 'Плейлист не найден');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_songs_sales', 'Общее количество песен продано');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_tickets_sales', 'Общая продажа билетов');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products_sales', 'Общая продукция продана');
            $lang_update_queries[] = PT_UpdateLangs($value, 'faqs', 'Часто задаваемые вопросы');
            $lang_update_queries[] = PT_UpdateLangs($value, 'yoomoney', 'Yoomoney');
            $lang_update_queries[] = PT_UpdateLangs($value, 'empty_amount', 'Сумма не может быть пустой');
            $lang_update_queries[] = PT_UpdateLangs($value, 'fortumo', 'Формумо');
            $lang_update_queries[] = PT_UpdateLangs($value, 'aamarpay', 'Аамарпай');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ngenius', 'Нгений');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinbase', 'Coinbase');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments', 'Coinpayments');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_approved', 'Ваш платеж с использованием CoinPayments был утвержден');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_canceled', 'Ваш платеж с использованием CoinPayments был отменен');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remember_device', 'Помните это устройство');
            $lang_update_queries[] = PT_UpdateLangs($value, 'least_characters', 'Должно быть не менее 6 символов длиной.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_lowercase', 'Должен содержать строчную букву.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_uppercase', 'Должен содержать заглавную букву.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'number_special', 'Должен содержать номер или особый характер.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'complexity_requirements', 'Поставляемый пароль не соответствует требованиям к минимальной сложности');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name', 'Имя');
            $lang_update_queries[] = PT_UpdateLangs($value, 'last_name', 'Фамилия');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name_last_name_empty', 'Ваше имя и фамилия не могут быть пустыми.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'terms_of_use_page', '<h4> 1- Напишите свои условия использования здесь. </h4> <br> <p> </p> <br> <p> lorem ipsum dolor sit amet, edisdpising elit, sed do eiusmod temper indicidunt ut labore et ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'about_page', '<h4> 1- Напишите свое о нас здесь. </h4> <br> <p> </p> <br> <p> lorem ipsum dolor sit amet, adisdpising elit, sed do eiusmod temper indicunt ut labore et dolore ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'privacy_policy_page', '<h4> 1- Напишите свою политику конфиденциальности здесь. </h4> <br> <p> </p> <br> <p> lorem ipsum dolor sit amet, edisdpising elit, sed do eiusmod temper indicunt ut labore et dolore ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'dmca_terms_page', '<h4> 1- Напишите свое уведомление DMCA. </h4> <br> <p> </p> <br> <p> lorem ipsum dolor sit amet, edisdpising elit, sed do eiusmod temper indicunt ut labore et dolore magna ');
        } else if ($value == 'spanish') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'playlist_not_found', 'Lista de reproducción no encontrada');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_songs_sales', 'Total de las canciones vendidas');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_tickets_sales', 'Venta de boletos totales');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products_sales', 'Productos totales vendidos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'faqs', 'Preguntas frecuentes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'yoomoney', 'Yoomoney');
            $lang_update_queries[] = PT_UpdateLangs($value, 'empty_amount', 'La cantidad no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ngenius', 'Nenio');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinbase', 'Coinbase');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments', 'Municipios');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_approved', 'Su pago utilizando CoinPayments ha sido aprobado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_canceled', 'Su pago utilizando CoinPayments ha sido cancelado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remember_device', 'Recuerde este dispositivo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'least_characters', 'Debe tener al menos 6 caracteres de largo.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_lowercase', 'Debe contener una letra minúscula.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_uppercase', 'Debe contener una letra mayúscula.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'number_special', 'Debe contener un número o carácter especial.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'complexity_requirements', 'La contraseña suministrada no cumple con los requisitos mínimos de complejidad');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name', 'Primer nombre');
            $lang_update_queries[] = PT_UpdateLangs($value, 'last_name', 'Apellido');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name_last_name_empty', 'Su primer nombre y apellido no pueden estar vacíos.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'terms_of_use_page', '<H4> 1- Escriba sus términos de uso aquí. </h4> <br> <p> </p> <br> <p> lorem ipsum dolor sit amet, consectetur adisdpising elit, sed do do temporal incididunt ut labore et labore et do ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'about_page', '<h4> 1- Escribe tu sobre nosotros aquí. </h4> <br> <p> </p> <br> <p> lorem ipsum dolor sit amet, consectetur adisdpising elit, sed do do temporal incididunt ut labore et dolorore ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'privacy_policy_page', '<h4> 1- Escriba su política de privacidad aquí. </h4> <br> <p> </p> <br> <p> lorem ipsum dolor sit amet, consectetur adisdpising elit, sed do do temporal incididunt ut labore et dolorore ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'dmca_terms_page', '<H4> 1- Escriba su aviso de DMCA. </h4> <br> <p> </p> <br> <p> lorem ipsum dolor sit amet, consectetur adisdpising elit, sed do do temporal incididunt ut labore et dolor Magna ');
        } else if ($value == 'turkish') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'playlist_not_found', 'Çalma listesi bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_songs_sales', 'Toplam şarkılar satıldı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_tickets_sales', 'Toplam bilet satışları');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products_sales', 'Satılan toplam ürünler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'faqs', 'SSS');
            $lang_update_queries[] = PT_UpdateLangs($value, 'yoomoney', 'Yoomoney');
            $lang_update_queries[] = PT_UpdateLangs($value, 'empty_amount', 'Miktar boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ngenius', 'Ngenius');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinbase', 'Paraya bakan');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments', 'Madeni para');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_approved', 'Coinpayments kullanarak ödemeniz onaylandı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_canceled', 'Coinpayments kullanarak ödemeniz iptal edildi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remember_device', 'Bu cihazı hatırla');
            $lang_update_queries[] = PT_UpdateLangs($value, 'least_characters', 'En az 6 karakter uzunluğunda olmalıdır.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_lowercase', 'Küçük bir harf içermelidir.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_uppercase', 'Bir büyük harf içermeli.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'number_special', 'Bir sayı veya özel karakter içermelidir.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'complexity_requirements', 'Sağlanan şifre, asgari karmaşıklık gereksinimlerini karşılamıyor');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name', 'İlk adı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'last_name', 'Soy isim');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name_last_name_empty', 'Adınız ve soyadınız boş olamaz.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'terms_of_use_page', '<h4> 1- Kullanım Koşullarınızı buraya yazın. ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'about_page', '<h4> 1- Bizim hakkımızda buraya yazın. ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'privacy_policy_page', '<h4> 1- Gizlilik politikanızı buraya yazın. ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'dmca_terms_page', '<h4> 1- DMCA bildiriminizi yazın. ');
        } else if ($value == 'english') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'playlist_not_found', 'Playlist not found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_songs_sales', 'Total Songs Sold');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_tickets_sales', 'Total Tickets Sales');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products_sales', 'Total Products Sold');
            $lang_update_queries[] = PT_UpdateLangs($value, 'faqs', 'Faqs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'yoomoney', 'Yoomoney');
            $lang_update_queries[] = PT_UpdateLangs($value, 'empty_amount', 'Amount can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ngenius', 'Ngenius');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinbase', 'Coinbase');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments', 'Coinpayments');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_approved', 'Your payment using CoinPayments has been approved');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_canceled', 'Your payment using CoinPayments has been canceled');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remember_device', 'Remember this device');
            $lang_update_queries[] = PT_UpdateLangs($value, 'least_characters', 'Must be at least 6 characters long.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_lowercase', 'Must contain a lowercase letter.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_uppercase', 'Must contain an uppercase letter.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'number_special', 'Must contain a number or special character.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'complexity_requirements', 'The password supplied does not meet the minimum complexity requirements');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name', 'First Name');
            $lang_update_queries[] = PT_UpdateLangs($value, 'last_name', 'Last Name');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name_last_name_empty', 'Your First Name and Last Name can not be empty.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'terms_of_use_page', '&lt;h4&gt;1- Write your Terms Of Use here.&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;h4&gt;2- Random title&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;');
            $lang_update_queries[] = PT_UpdateLangs($value, 'about_page', '&lt;h4&gt;1- Write your About us here.&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; <br>&lt;p&gt;&lt;br /&gt; &lt;br /&gt; &lt;/p&gt; <br>&lt;h4&gt;2- Random title&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;');
            $lang_update_queries[] = PT_UpdateLangs($value, 'privacy_policy_page', '&lt;h4&gt;1- Write your Privacy Policy here.&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; <br>&lt;p&gt;&lt;br /&gt; &lt;br /&gt; &lt;/p&gt; <br>&lt;h4&gt;2- Random title&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;');
            $lang_update_queries[] = PT_UpdateLangs($value, 'dmca_terms_page', '&lt;h4&gt;1- Write your DMCA Notice.&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; <br>&lt;p&gt;&lt;br /&gt; &lt;br /&gt; &lt;/p&gt; <br>&lt;h4&gt;2- Random title&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;');
        } else if ($value != 'english') {
            $lang_update_queries[] = PT_UpdateLangs($value, 'playlist_not_found', 'Playlist not found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_songs_sales', 'Total Songs Sold');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_tickets_sales', 'Total Tickets Sales');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products_sales', 'Total Products Sold');
            $lang_update_queries[] = PT_UpdateLangs($value, 'faqs', 'Faqs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'yoomoney', 'Yoomoney');
            $lang_update_queries[] = PT_UpdateLangs($value, 'empty_amount', 'Amount can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'fortumo', 'Fortumo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'aamarpay', 'Aamarpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ngenius', 'Ngenius');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinbase', 'Coinbase');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments', 'Coinpayments');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_approved', 'Your payment using CoinPayments has been approved');
            $lang_update_queries[] = PT_UpdateLangs($value, 'coinpayments_canceled', 'Your payment using CoinPayments has been canceled');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remember_device', 'Remember this device');
            $lang_update_queries[] = PT_UpdateLangs($value, 'least_characters', 'Must be at least 6 characters long.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_lowercase', 'Must contain a lowercase letter.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'contain_uppercase', 'Must contain an uppercase letter.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'number_special', 'Must contain a number or special character.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'complexity_requirements', 'The password supplied does not meet the minimum complexity requirements');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name', 'First Name');
            $lang_update_queries[] = PT_UpdateLangs($value, 'last_name', 'Last Name');
            $lang_update_queries[] = PT_UpdateLangs($value, 'first_name_last_name_empty', 'Your First Name and Last Name can not be empty.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'terms_of_use_page', '&lt;h4&gt;1- Write your Terms Of Use here.&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;h4&gt;2- Random title&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;');
            $lang_update_queries[] = PT_UpdateLangs($value, 'about_page', '&lt;h4&gt;1- Write your About us here.&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; <br>&lt;p&gt;&lt;br /&gt; &lt;br /&gt; &lt;/p&gt; <br>&lt;h4&gt;2- Random title&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;');
            $lang_update_queries[] = PT_UpdateLangs($value, 'privacy_policy_page', '&lt;h4&gt;1- Write your Privacy Policy here.&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; <br>&lt;p&gt;&lt;br /&gt; &lt;br /&gt; &lt;/p&gt; <br>&lt;h4&gt;2- Random title&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;');
            $lang_update_queries[] = PT_UpdateLangs($value, 'dmca_terms_page', '&lt;h4&gt;1- Write your DMCA Notice.&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adisdpisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis sdnostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt; <br>&lt;p&gt;&lt;br /&gt; &lt;br /&gt; &lt;/p&gt; <br>&lt;h4&gt;2- Random title&lt;/h4&gt; <br>&lt;p&gt; &lt;/p&gt; <br>&lt;p&gt;Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.&lt;/p&gt;');
        }
    }
    if (!empty($lang_update_queries)) {
        foreach ($lang_update_queries as $key => $query) {
            $sql = mysqli_query($mysqli, $query);
        }
    }
    $name = md5(microtime()) . '_updated.php';
    rename('update.php', $name);
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
                     <h2 class="light">Update to v1.4.6 </span></h2>
                     <div class="setting-well">
                        <h4>Changelog</h4>
                        <ul class="wo_update_changelog">
                          <li>[Added] new left sidebar on volcano theme.</li>
                          <li>[Added] new discover page on default theme.</li>
                          <li>[Added] Yoomoney, Fortumo, Aamarpay, Ngenius, CoinPayments, Coinbase payments gateways.</li>
                          <li>[Added] the ability to re-arrange playlist songs.</li>
                          <li>[Added] the ability to translate terms page.</li>
                          <li>[Added] Remember This Device option to login page. </li>
                          <li>[Added] Password Complexity System to registration page. </li>
                          <li>[Added] Auto Username to registration page. </li>
                          <li>[Added] the ability to translate SEO details. </li>
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
    "UPDATE `config` SET value = '1.4.6' WHERE name = 'version';",
    "CREATE TABLE `faqs` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `question` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `answer` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `time` INT(11) NULL DEFAULT '0' , PRIMARY KEY (`id`)) ENGINE = InnoDB;",
    "ALTER TABLE `users` ROW_FORMAT=DYNAMIC;",
    "ALTER TABLE `users` ADD `cashfree_key` INT(30) NOT NULL DEFAULT '0' AFTER `securionpay_key`, ADD INDEX (`cashfree_key`);",
    "ALTER TABLE `users` ADD `yoomoney_hash` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `cashfree_key`, ADD INDEX (`yoomoney_hash`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'fortumo_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'fortumo_service_id', '');",
    "ALTER TABLE `users` ADD `fortumo_hash` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `yoomoney_hash`;",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'aamarpay_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'aamarpay_mode', 'sandbox');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'aamarpay_store_id', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'aamarpay_signature_key', '');",
    "ALTER TABLE `users` ADD `aamarpay_tran_id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `fortumo_hash`, ADD INDEX (`aamarpay_tran_id`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'ngenius_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'ngenius_mode', 'sandbox');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'ngenius_api_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'ngenius_outlet_id', '');",
    "ALTER TABLE `users` ADD `ngenius_ref` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `aamarpay_tran_id`, ADD INDEX (`ngenius_ref`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'coinbase_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'coinbase_key', '');",
    "ALTER TABLE `users` ADD `coinbase_hash` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `ngenius_ref`, ADD `coinbase_code` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `coinbase_hash`, ADD INDEX (`coinbase_hash`), ADD INDEX (`coinbase_code`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'coinpayments', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'coinpayments_secret', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'coinpayments_public_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'coinpayments_coin', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'coinpayments_coins', '');",
    "ALTER TABLE `users` ADD `coinpayments_txn_id` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `coinbase_code`, ADD INDEX (`coinpayments_txn_id`);",
    "ALTER TABLE `notifications` CHANGE `type` `type` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '';",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'password_complexity_system', '0');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'remember_device', '0');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'auto_username', '0');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'yoomoney_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'yoomoney_wallet_id', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'yoomoney_notifications_secret', '');",
    "UPDATE `config` SET `value` = '{\"ads\":{\"title\":\"{LANG_KEY Advertising} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"albums\":{\"title\":\"{LANG_KEY Albums} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"become\":{\"title\":\"{LANG_KEY Become an artist} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"blogs\":{\"title\":\"{LANG_KEY Blogs} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"checkout\":{\"title\":\"{LANG_KEY Checkout} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"contact\":{\"title\":\"{LANG_KEY Contact} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"create-ads\":{\"title\":\"{LANG_KEY Create Advertising} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"create-article\":{\"title\":\"{LANG_KEY Create New Article} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"create-event\":{\"title\":\"{LANG_KEY Create Event} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"create-product\":{\"title\":\"{LANG_KEY Create Product} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"create_story\":{\"title\":\"{LANG_KEY Create Story} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"customer_orders\":{\"title\":\"{LANG_KEY My Orders} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"dashboard\":{\"title\":\"{LANG_KEY Dashboard} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"discover\":{\"title\":\"{LANG_KEY Discover} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"events\":{\"title\":\"{LANG_KEY Events} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"fame\":{\"title\":\"{LANG_KEY Hall of fame} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"favourites\":{\"title\":\"{LANG_KEY Favourites} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"feed\":{\"title\":\"{LANG_KEY Feed} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"forgot-password\":{\"title\":\"Forgot Password - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"genres\":{\"title\":\"{LANG_KEY Genres} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"go-pro\":{\"title\":\"{LANG_KEY Go Pro!} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"home\":{\"title\":\"{LANG_KEY Home} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"import\":{\"title\":\"{LANG_KEY Import} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"maintenance\":{\"title\":\"{LANG_KEY Maintenance} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"manage_events\":{\"title\":\"{LANG_KEY Manage Events} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"manage_products\":{\"title\":\"{LANG_KEY Manage Products} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"messages\":{\"title\":\"{LANG_KEY Messages} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"my_playlists\":{\"title\":\"{LANG_KEY Playlists} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"new_music\":{\"title\":\"{LANG_KEY New Music} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"not-found\":{\"title\":\"404\",\"meta_keywords\":\"{SITE_KEYWORDS} - {SITE_TITLE}\",\"meta_description\":\"{SITE_DESC}\"},\"orders\":{\"title\":\"{LANG_KEY Orders} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"payment-error\":{\"title\":\"{LANG_KEY Payment Error} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"playlists\":{\"title\":\"{LANG_KEY Playlists} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"point-system\":{\"title\":\"{LANG_KEY Points System} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"purchased\":{\"title\":\"{LANG_KEY Purchased Songs} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"recently_played\":{\"title\":\"{LANG_KEY Recently Played} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"reset-password\":{\"title\":\"Reset Password - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"search\":{\"title\":\"{LANG_KEY Search} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"settings\":{\"title\":\"{LANG_KEY Settings} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"spotlight\":{\"title\":\"{LANG_KEY Spotlight} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"stations\":{\"title\":\"{LANG_KEY Stations} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"store\":{\"title\":\"{LANG_KEY Store} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"top-genres\":{\"title\":\"{LANG_KEY Top Music} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"top_music\":{\"title\":\"{LANG_KEY Top Music} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"top_music_album\":{\"title\":\"{LANG_KEY Top Albums} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"upgraded\":{\"title\":\"{LANG_KEY You are a pro!} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"upload-album\":{\"title\":\"{LANG_KEY Upload} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"},\"upload-song\":{\"title\":\"{LANG_KEY Upload} - {SITE_TITLE}\",\"meta_keywords\":\"{SITE_KEYWORDS}\",\"meta_description\":\"{SITE_DESC}\"}}' WHERE `config`.`name` = 'seo';",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'playlist_not_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_songs_sales');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_tickets_sales');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_products_sales');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'faqs');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'yoomoney');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'empty_amount');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'fortumo');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'aamarpay');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'ngenius');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'coinbase');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'coinpayments');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'coinpayments_approved');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'coinpayments_canceled');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'remember_device');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'least_characters');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'contain_lowercase');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'contain_uppercase');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'number_special');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'complexity_requirements');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'first_name');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'last_name');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'first_name_last_name_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'terms_of_use_page');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'about_page');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'privacy_policy_page');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'dmca_terms_page');",

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
