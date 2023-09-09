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
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_added_successfully_', 'تم إضافة عنوانك بنجاح!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_edited_successfully_', 'تم تحرير عنوانك بنجاح!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_name_must_be_between_5_32_', 'يجب أن يكون الاسم بين 5/32');
            $lang_update_queries[] = PT_UpdateLangs($value, '_the_url_is_invalid._please_enter_a_valid_url_', 'عنوان URL غير صالح، يرجى إدخال عنوان URL صالح.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image___video_', 'ملف الوسائط غير صالح، يرجى تحديد صورة صالحة / فيديو صالحة.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image_', 'ملف الوسائط غير صالح، يرجى تحديد صورة صالحة.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_audio_', 'ملف الوسائط غير صالح، يرجى تحديد ملف صوتي صالح.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_too_many_login_attempts_please_try_again_later_', 'الكثير من محاولات تسجيل الدخول، يرجى المحاولة مرة أخرى لاحقا.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_url_can_not_be_empty_', 'عنوان URL لا يمكن أن يكون فارغا.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_address_can_not_be_empty_', 'العنوان لا يمكن أن تكون فارغة.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tickets_available_and_ticket_price_can_not_be_empty_', 'لا يمكن أن يكون توافر التذاكر والسعر فارغا.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_cover_can_not_be_empty_', 'غطاء الحدث مطلوب.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_video_can_not_be_empty_', 'مقطع الفيديو الحدث مطلوب.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_published_successfully_', 'تم نشر حدثك بنجاح!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_updated_successfully_', 'تم تحديث الحدث الخاص بك بنجاح!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_payment_successfully_done_', 'الدفع بنجاح، شكرا لك!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_song_', 'يرجى اختيار أغنية.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_valid_image_', 'يرجى تحديد صورة صالحة.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_published_successfully_', 'تم نشر منتجك بنجاح!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_is_under_review_', 'يتم تقديم منتجك وسيتم مراجعته قريبا.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_edited_successfully_', 'تم تحرير منتجك بنجاح!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_some_products_don_t_have_enough_of_units_', 'بعض منتجاتك ليس لديها وحدات كافية.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_wallet_', 'ليس لديك رصيد كاف في محفظتك.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_top_up_your_wallet_', 'يرجى تباريز محفظتك');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_order_has_been_placed_successfully_', 'وقد وضعت طلبك!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tracking_info_has_been_saved_successfully_', 'تم حفظ معلومات التتبع.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_review_has_been_sent_successfully_', 'تم إرسال المراجعة.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_request_is_under_review_', 'طلبك قيد المراجعة.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_published_successfully_', 'تم نشر قصتك بنجاح!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_uploaded_successfully_to_publish_it_please_pay_', 'تم تحميل قصتك، يرجى الدفع للمتابعة.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_story_not_found_or_its_active_', 'قصة غير موجودة أو غير نشطة.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_money_please_top_up_your_wallet_', 'ليس لديك رصيد كاف في محفظتك، يرجى تباريز محفظتك.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_linkedin', 'تسجيل الدخول مع ينكدين');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_vkontakte', 'تسجيل الدخول مع vkontakte.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_instagram', 'تسجيل الدخول مع Instagram.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_qq', 'تسجيل الدخول مع QQ.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_wechat', 'تسجيل الدخول مع wechat.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_discord', 'تسجيل الدخول مع الخلاف');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_mailru', 'تسجيل الدخول مع mailru.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_items_found', 'لم يتم العثور على العناصر.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_wallet', 'ليس لديك رصيد كاف في محفظتك.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_top_up_your_wallet', 'يرجى تباريز محفظتك.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total', 'المجموع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new_address', 'إضافة عنوان جديد');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_new_event', 'إنشاء حدث جديد');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_events', 'إدارة الأحداث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'browse_events', 'تصفح الأحداث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_events', 'انضم الأحداث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_purchased_tickets', 'عرض التذاكر المشتراة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name', 'اسم الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location', 'موقع الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'online', 'متصل');
            $lang_update_queries[] = PT_UpdateLangs($value, 'real_location', 'الموقع الحقيقي');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_date', 'تاريخ بدء الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_time', 'وقت بدء الوقت');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_date', 'تاريخ انتهاء الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_time', 'نهاية الوقت الوقت');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone', 'وحدة زمنية');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_tickets', 'بيع تذاكر');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tickets_available_total_tickets_available_for_this_event_', 'التذاكر المتاحة (إجمالي التذاكر المتاحة لهذا الحدث)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_price', 'سعر التذكرة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description', 'وصف الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_cover', 'غطاء الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_video_trailer', 'فيديو فيديو / مقطورة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_product', 'إنشاء المنتج');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_products', 'إدارة المنتجات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_item_units', 'وحدات البند الإجمالية');
            $lang_update_queries[] = PT_UpdateLangs($value, 'related_to_song', 'المتعلقة الأغنية');
            $lang_update_queries[] = PT_UpdateLangs($value, 'images', 'صور');
            $lang_update_queries[] = PT_UpdateLangs($value, 'who_can_see', 'من يستطيع رؤيته');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_my_followers_only', 'عرض لأتببي');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_all_users', 'عرض لجميع المستخدمين (الترويج)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_image', 'قصة صورة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_song', 'تحميل الأغنية');
            $lang_update_queries[] = PT_UpdateLangs($value, 'shipped', 'شحنها');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivered', 'تم التوصيل');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payments', 'المدفوعات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'subtotal', 'فرعي');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund_money', 'استرداد المال');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_details', 'تتبع التفاصيل');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_url', 'URL الموقع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number', 'رقم التعقب');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivery_address', 'عنوان التسليم');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_orders_found', 'لم يتم العثور على أية طلبات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'products', 'منتجات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_details', 'عرض التفاصيل');
            $lang_update_queries[] = PT_UpdateLangs($value, 'stories', 'قصص');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined', 'انضم');
            $lang_update_queries[] = PT_UpdateLangs($value, 'join', 'انضم');
            $lang_update_queries[] = PT_UpdateLangs($value, 'buy_a_ticket', 'شراء تذكرة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_trailer', 'عرض مقطورة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_event', 'تحرير الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date', 'تاريخ البدء');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date', 'تاريخ الانتهاء');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_tickets', 'التذاكر المتاحة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_people', 'انضم الناس');
            $lang_update_queries[] = PT_UpdateLangs($value, 'location', 'موقع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_events', 'إجمالي الأحداث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_joined_users', 'إجمالي مستودع المستخدمين');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_available_tickets', 'إجمالي التذاكر المتاحة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_joined_events', 'الأكثر انضمام الأحداث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_events', 'الأحداث الأكثر مبيعا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_events_found', 'لم يتم العثور على المزيد من الأحداث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_tickets_found', 'لم يتم العثور على المزيد من التذاكر');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_products_found', 'لم يتم العثور على المزيد من المنتجات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_reviews_found', 'لم يتم العثور على المزيد من المراجعات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_successfully_done', 'دفع بنجاح');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_song_', 'هل أنت متأكد أنك تريد أن تدفع لشراء هذه الأغنية؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_album_', 'هل أنت متأكد أنك تريد أن تدفع لشراء هذا الألبوم؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_upgrade_to_pro_', 'هل أنت متأكد أنك تريد الترقية إلى الموالية؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_money_please_top_up_your_wallet', 'ليس لديك ما يكفي من المال يرجى توبيخ محفظتك');
            $lang_update_queries[] = PT_UpdateLangs($value, 'interested', 'مهتم');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_views', 'لا مزيد من المشاهدات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_your_story_', 'هل أنت متأكد أنك تريد حذف قصتك؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_add_a_new_address', 'يرجى إضافة عنوان جديد');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_address', 'يرجى اختيار العنوان');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund', 'استرداد');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_event', 'انشاء حدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'checkout', 'الدفع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_orders', 'أوامر المتجر');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_orders', 'طلباتي');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_request_found', 'لم يتم العثور على طلب');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_event', 'حذف الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'cashfree', 'cashfree.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paystack', 'Paystack.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'razorpay', 'Razorpay.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paysera', 'Paysera.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iyzipay', 'iyzipay.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payu', 'بدين');
            $lang_update_queries[] = PT_UpdateLangs($value, 'securionpay', 'securionpay.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'authorize', 'أذن');
            $lang_update_queries[] = PT_UpdateLangs($value, 'placed', 'وضع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'canceled', 'ألغيت');
            $lang_update_queries[] = PT_UpdateLangs($value, 'packed', 'معباه');
            $lang_update_queries[] = PT_UpdateLangs($value, 'commission', 'لجنة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'final_price', 'السعر النهائي');
            $lang_update_queries[] = PT_UpdateLangs($value, 'link', 'وصلة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_commission', 'مفوضية الموقع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'currently_unavailable.', 'غير متاح حاليا.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'write_review', 'أكتب مراجعة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'photos', 'الصور');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_purchase', 'التحقق من الشراء');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events', 'الأحداث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_addresses', 'عناويني');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new', 'اضف جديد');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_address', 'تعديل العنوان');
            $lang_update_queries[] = PT_UpdateLangs($value, 'postcode___zip', 'الرمز البريدي / الرمز البريدي');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invitation_links', 'روابط الدعوة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_links', 'الروابط المتاحة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generated_links', 'روابط ولدت');
            $lang_update_queries[] = PT_UpdateLangs($value, 'used_links', 'روابط مستعملة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_link', 'توليد رابط');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invited_user', 'دعوة المستخدم');
            $lang_update_queries[] = PT_UpdateLangs($value, 'date', 'تاريخ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copy', 'ينسخ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copied', 'نسخ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_wallet', 'المحفظة المتاحة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_up_wallet', 'أعلى المحفظة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'hall_of_fame', 'قاعة الشهرة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'analytics', 'تحليلات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'more_info', 'مزيد من المعلومات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'listen_in_youtube', 'اسمع في يوتيوب');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tagged_artists', 'الفنانين الموسومة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate', 'يتبرع');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_other', 'آخر');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_clothes', 'ملابس');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_electronic', 'الإلكترونية');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remove_from_cart', 'إزالة من العربة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_to_cart', 'أضف إلى السلة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_cart_is_empty.', 'عربة التسوق فارغة.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_address', 'حذف عنوانك');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_address_', 'هل أنت متأكد من أنك تريد حذف هذا العنوان؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_alert', 'تنبيه الدفع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_', 'هل أنت متأكد أنك تريد أن تدفع؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_product', 'حذف المنتج الخاص بك');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_product_', 'هل أنت متأكد أنك تريد حذف هذا المنتج؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_for_story', 'دفع ثمن القصة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_for_create_story_', 'هل أنت متأكد أنك تريد أن تدفع مقابل قصة خلق؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_from_wallet', 'دفع من المحفظة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_buy_a_ticket_', 'هل أنت متأكد أنك تريد شراء تذكرة؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave_event', 'حدث المغادرة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_leave_this_event_', 'هل أنت متأكد أنك تريد أن تترك هذا الحدث؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave', 'يترك');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_event', 'حذف الحدث الخاص بك');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_event_', 'هل أنت متأكد من حذف هذا الحدث؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'الحصول على التحقق، وبيع أغانيك، وبيع المنتجات، وخلق أحداث وبيع تذاكر، والحصول على ملف تعريف خاص ويشتهر في منصاتنا!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'الحصول على التحقق، وبيع أغانيك، وخلق الأحداث وبيع التذاكر، والحصول على ملف تعريف خاص ويشتهر في منصة لدينا!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'الحصول على التحقق، وبيع أغانيك، وبيع المنتجات، والحصول على ملف تعريف خاص ويشتهر في منصتنا!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'الحصول على التحقق، وبيع أغانيك، والحصول على ملف تعريف خاص ويشتهر في منصتنا!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_events_found', 'لا توجد أحداث وجدت');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event', 'حدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product', 'المنتج');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate_button', 'تبرع زر');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_information', 'معلوماتي');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_choose_which_information_you_would_like_to_download', 'يرجى اختيار المعلومات التي ترغب في تنزيلها.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_file', 'توليد الملف');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_published_successfully', 'تم نشر الحدث الخاص بك بنجاح');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_tickets_found', 'لم يتم العثور على تذاكر');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_tickets', 'اشترى التذاكر');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_updated_successfully', 'تم تحديث حدثك بنجاح');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_is_under_review', 'المنتج الخاص بك قيد المراجعة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_published_successfully', 'تم نشر منتجك بنجاح');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_product', 'تحرير المنتج');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_edited_successfully', 'تم تحرير منتجك بنجاح');
            $lang_update_queries[] = PT_UpdateLangs($value, 'guest', 'ضيف');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket', 'تذكرة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events_analytics', 'أحداث التحليلات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id', 'هوية شخصية');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_artists', 'فنانين العلامة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_other_artists_to_show_they_performed_together', 'علامة الفنانين الآخرين لإظهار أنها تؤدي معا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_ticket', 'تحميل تذكرة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_order_has_been_placed_successfully', 'تم وضع طلبك بنجاح');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order', 'ترتيب');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sale_invoice', 'الفاتورة بيع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_name', 'البائع اسم');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_email', 'البريد الإلكتروني للبائع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invoice_to', 'فاتورة إلى');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_details', 'بيانات الدفع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_due', 'الاجمالي المستحق');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank_name', 'اسم البنك');
            $lang_update_queries[] = PT_UpdateLangs($value, 'item', 'غرض');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_invoice', 'تحميل فاتورة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'details', 'تفاصيل');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_products_found', 'لم يتم العثور على المنتجات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_reviews_found', 'لم يتم العثور على مراجعات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_about_to_purchase_the_items__do_you_want_to_proceed_', 'أنت على وشك شراء العناصر، هل تريد المتابعة؟');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request_a_refund', 'طلب استرداد');
            $lang_update_queries[] = PT_UpdateLangs($value, 'new_orders_has_been_placed', 'تم وضع أوامر جديدة.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_status_has_been_changed', 'تم تحديث حالة طلبك.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_declined', 'تم رفض طلب استرداد الأموال الخاص بك.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_approved_your_money_added_to_your_wallet', 'تمت الموافقة على طلب استرداد الأموال الخاص بك، الرصيد إعادة إضافته إلى محفظتك.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'added_tracking_info', 'تحديث الطلب مع معلومات التتبع.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_approved', 'تمت الموافقة على منتجك.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_your_event', 'انضم إلى حدثك');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bought_a_ticket', 'اشترى تذكرة، حصلت على بيع جديد!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'orders', 'الطلب #٪ s');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_purchased', 'أنت لم تشتري هذا البند.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_not_found', 'غير موجود');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_status_wasn_t_set_to_delivered_within_60_days_from_the_order_date__it_will_be_automatically_be_sent_to__delivered_.', 'إذا لم يتم تعيين حالة الطلب لتسليمها في غضون 60 يوما من تاريخ الطلب، فسيتم تعيينه تلقائيا لتسليمه.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_wasn_t_actually_delivered__the_buyer_can_request_a_refund.', 'إذا لم يتم تسليم الطلب بالفعل، يمكن للمشتري طلب استرداد.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_request_is_under_review', 'طلبك قيد المراجعة.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request', 'طلب');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_explain_the_reason', 'يرجى توضيح السبب');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_products', 'أعلى المنتجات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___products_this_week', 'أفضل الأغاني والمنتجات الأكثر مبيعا هذا الأسبوع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___albums_this_week', 'أفضل الأغاني والألبومات هذا الأسبوع');
            $lang_update_queries[] = PT_UpdateLangs($value, 'accepted_', 'وافقت');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_wait__this_may_take_few_minutes.', 'يرجى الانتظار، قد يستغرق هذا بضع دقائق.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'instead_of_uploading_a_song__you_can_easily_import_songs_using', 'بدلا من تحميل أغنية، يمكنك بسهولة استيراد الأغاني باستخدام');
            $lang_update_queries[] = PT_UpdateLangs($value, 'imported_a_new_song_', 'استوردت أغنية جديدة،');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_has_been_sent_successfully', 'تم إرسال مراجعة بنجاح!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product_', 'خلق منتج جديد،');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_event_', 'تم إنشاء حدث جديد،');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_new_event_', 'انضم الحدث الجديد،');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_a_ticket_', 'اشترت تذكرة،');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_store', 'متجري');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_analytics', 'تخزين التحليلات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products', 'مجموع المنتجات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_earned', 'إجمالي المكتسبة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_commission', 'اللجنة الإجمالية');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_sub_earned', 'مجموع الفرعية المكتسبة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_products', 'الأكثر مبيعا المنتجات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name_can_not_be_empty', 'اسم الحدث لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description_can_not_be_empty', 'وصف الحدث لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date_can_not_be_empty', 'تاريخ البدء لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_story', 'إنشاء قصة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_related_song_can_not_be_empty', 'أغنية ذات الصلة المنتج لا يمكن أن تكون فارغة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_info', 'معلومات المنتج');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_info', 'معلومات الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_the_owner', 'أنت لست المالك');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_not_found', 'لم يتم العثور على الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'this_event_is_free', 'هذا الحدث مجاني');
            $lang_update_queries[] = PT_UpdateLangs($value, 'there_is_no_available_tickets', 'لا توجد تذاكر متاحة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'card_is_empty', 'البطاقة فارغة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_can_not_be_empty', 'العنوان لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id_can_not_be_empty', 'معرف لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location_can_not_be_empty', 'موقع الحدث لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_time_can_not_be_empty', 'وقت البدء لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date_can_not_be_empty', 'تاريخ الانتهاء لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_time_can_not_be_empty', 'نهاية الوقت لا يمكن أن تكون فارغة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone_can_not_be_empty', 'timezone لا يمكن أن تكون فارغة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_image_can_not_be_empty', 'صورة الحدث لا يمكن أن تكون فارغة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_title_can_not_be_empty', 'عنوان المنتج لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_description_can_not_be_empty', 'وصف المنتج لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_tags_can_not_be_empty', 'علامات المنتج لا يمكن أن تكون فارغة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_price_can_not_be_empty', 'سعر المنتج لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_units_can_not_be_empty', 'وحدات المنتج لا يمكن أن تكون فارغة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_category_can_not_be_empty', 'فئة المنتج لا يمكن أن تكون فارغة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_image_can_not_be_empty', 'صورة المنتج لا يمكن أن تكون فارغة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_not_found', 'الصنف غير موجود');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_not_found', 'لم يتم العثور على العنوان');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_url_can_not_be_empty', 'لا يمكن أن يكون تتبع عنوان URL فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number_can_not_be_empty', 'عدد تتبع لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_enter_a_valid_url', 'أدخل رابط صحيح من فضلك');
            $lang_update_queries[] = PT_UpdateLangs($value, 'rating_can_not_be_empty', 'التصنيف لا يمكن أن يكون فارغا');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_can_not_be_empty', 'مراجعة لا يمكن أن تكون فارغة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_who_can_see_the_story', 'من فضلك من يستطيع رؤية القصة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_image', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_song', 'يرجى اختيار أغنية قصة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_not_found_or_its_not_active', 'القصة غير موجودة أو غير نشطة');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_your_songs', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_products', 'بيع المنتجات');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_events_and_sell_tickets', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_more_songs', 'تحميل المزيد من الأغاني');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_more_space', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_a_special_looking_profile_and_get_famous_on_our_platform_', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_was_purchased_in_sitename__date', 'تم شراء تذكرة في {SITENAME}، {DATE}');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'track', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ticket', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'for', 'ل');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_starts', 'يبدأ الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ends', 'ينتهي الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'video_duration_must_be_less_than_or_equal_10_seconds', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_by', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_address', 'عنوان الحدث');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_orders_found', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_to_purchase', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_video_will_be_converted_to_mp3_soon__you_ll_get_notified_once_imported', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_song_is_ready_to_view', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'Reviews', '');
        } else if ($value == 'dutch') {
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_added_successfully_', 'Uw adres is succesvol toegevoegd!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_edited_successfully_', 'Uw adres is succesvol bewerkt!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_name_must_be_between_5_32_', 'Naam moet tussen 5/32 zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, '_the_url_is_invalid._please_enter_a_valid_url_', 'De URL is ongeldig, voer een geldige URL in.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image___video_', 'Mediabestand is ongeldig, selecteer een geldige afbeelding / video.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image_', 'Mediabestand is ongeldig, selecteer een geldige afbeelding.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_audio_', 'Mediabestand is ongeldig, selecteer een geldig audiobestand.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_too_many_login_attempts_please_try_again_later_', 'Te veel inlogpogingen, probeer het later opnieuw.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_url_can_not_be_empty_', 'URL kan niet leeg zijn.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_address_can_not_be_empty_', 'Adres kan niet leeg zijn.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tickets_available_and_ticket_price_can_not_be_empty_', 'Tickets Beschikbaarheid en prijs kan niet leeg zijn.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_cover_can_not_be_empty_', 'Evenementbedekking is vereist.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_video_can_not_be_empty_', 'Evenement-video is vereist.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_published_successfully_', 'Uw evenement is succesvol gepubliceerd!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_updated_successfully_', 'Uw evenement is succesvol bijgewerkt!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_payment_successfully_done_', 'Betaling succesvol, dank u!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_song_', 'Selecteer een nummer.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_valid_image_', 'Selecteer een geldige afbeelding.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_published_successfully_', 'Uw product is succesvol gepubliceerd!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_is_under_review_', 'Uw product wordt ingediend en wordt binnenkort beoordeeld.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_edited_successfully_', 'Uw product is succesvol bewerkt!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_some_products_don_t_have_enough_of_units_', 'Sommige van uw producten hebben niet genoeg eenheden.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_wallet_', 'Je hebt niet genoeg balans in je portemonnee.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_top_up_your_wallet_', 'Vul uw portemonnee bij');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_order_has_been_placed_successfully_', 'Uw bestelling is geplaatst!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tracking_info_has_been_saved_successfully_', 'Tracking-informatie is opgeslagen.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_review_has_been_sent_successfully_', 'Beoordeling is verzonden.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_request_is_under_review_', 'Uw aanvraag wordt beoordeeld.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_published_successfully_', 'Je verhaal is succesvol gepubliceerd!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_uploaded_successfully_to_publish_it_please_pay_', 'Uw verhaal is geüpload, betaal alstublieft om door te gaan.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_story_not_found_or_its_active_', 'Verhaal niet gevonden of niet actief.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_money_please_top_up_your_wallet_', 'Je hebt niet genoeg balans in je portemonnee, vul je portemonnee aan.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_linkedin', 'Log in met LinkedIn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_vkontakte', 'Log in met VKONTAKTE');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_instagram', 'Log in met Instagram');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_qq', 'Login met QQ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_wechat', 'Login met Wechat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_discord', 'Inloggen met Discord');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_mailru', 'Login met MailRU');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_items_found', 'Geen items gevonden.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_wallet', 'Je hebt niet genoeg balans in je portemonnee.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_top_up_your_wallet', 'Vul uw portemonnee bij.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total', 'Totaal');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new_address', 'Nieuw adres toevoegen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_new_event', 'Maak een nieuw evenement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_events', 'Beheer evenementen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'browse_events', 'Blader door evenementen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_events', 'Gesloten evenementen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_purchased_tickets', 'Bekijk gekochte tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name', 'Evenement naam');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location', 'Evenementlocatie');
            $lang_update_queries[] = PT_UpdateLangs($value, 'online', 'Online');
            $lang_update_queries[] = PT_UpdateLangs($value, 'real_location', 'Echte locatie');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_date', 'Evenement startdatum');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_time', 'Evenement starttijd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_date', 'Evenement-einddatum');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_time', 'Etentijd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone', 'Tijdzone');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_tickets', 'Verkoop tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tickets_available_total_tickets_available_for_this_event_', 'Tickets beschikbaar (totale tickets beschikbaar voor dit evenement)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_price', 'Ticket prijs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description', 'beschrijving van het evenement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_cover', 'Gebeurtenisdekking');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_video_trailer', 'Evenement Video / Trailer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_product', 'Maak product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_products', 'Producten beheren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_item_units', 'Totale itemeenheden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'related_to_song', 'Gerelateerd aan liedje');
            $lang_update_queries[] = PT_UpdateLangs($value, 'images', 'Afbeeldingen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'who_can_see', 'Wie kan zien');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_my_followers_only', 'Toon aan mijn volgers');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_all_users', 'Toon aan alle gebruikers (promotie)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_image', 'Verhaalbeeld');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_song', 'Upload Song');
            $lang_update_queries[] = PT_UpdateLangs($value, 'shipped', 'Verzenden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivered', 'Afgeleverd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payments', 'Betalingen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'subtotal', 'Subtotaal');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund_money', 'Restitutie geld');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_details', 'Tracking details');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_url', 'Site URL');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number', 'Volg nummer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivery_address', 'Bezorgadres');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_orders_found', 'Geen bestellingen gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'products', 'Producten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_details', 'Bekijk details');
            $lang_update_queries[] = PT_UpdateLangs($value, 'stories', 'Verhalen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined', 'Toegetreden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'join', 'Meedoen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'buy_a_ticket', 'Koop een kaartje');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_trailer', 'Bekijken');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_event', 'Bewerkingsgebeurtenis');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date', 'Startdatum');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date', 'Einddatum');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_tickets', 'Beschikbare tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_people', 'Kwam bij mensen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'location', 'Plaats');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_events', 'Totaal evenementen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_joined_users', 'Totaal toegevoegde gebruikers');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_available_tickets', 'Totaal beschikbare tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_joined_events', 'Meest gevonden evenementen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_events', 'Meest verkochte evenementen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_events_found', 'Geen evenementen meer gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_tickets_found', 'Geen tickets meer gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_products_found', 'Geen producten gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_reviews_found', 'Geen beoordelingen gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_successfully_done', 'Betaling succesvol gedaan');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_song_', 'Weet je zeker dat je wilt betalen om dit nummer te kopen?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_album_', 'Weet je zeker dat je wilt betalen om dit album te kopen?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_upgrade_to_pro_', 'Weet je zeker dat je wilt upgraden naar Pro?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_money_please_top_up_your_wallet', 'Je hebt niet genoeg geld, vul je portemonnee bij');
            $lang_update_queries[] = PT_UpdateLangs($value, 'interested', 'Geïnteresseerd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_views', 'Geen weergaven');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_your_story_', 'Weet je zeker dat je je verhaal wilt verwijderen?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_add_a_new_address', 'Voeg alstublieft een nieuw adres toe');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_address', 'Selecteer adres');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund', 'Terugbetaling');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_event', 'Creëer evenement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'checkout', 'Uitchecken');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_orders', 'Bestellingen opslaan');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_orders', 'mijn bestellingen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_request_found', 'Geen aanvraag gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_event', 'Evenement verwijderen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'cashfree', 'Cashfree');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paystack', 'Paystack');
            $lang_update_queries[] = PT_UpdateLangs($value, 'razorpay', 'Razorpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paysera', 'Salarisa');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iyzipay', 'Iyzipay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payu', 'Payu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'securionpay', 'Securionpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'authorize', 'Toestemming geven');
            $lang_update_queries[] = PT_UpdateLangs($value, 'placed', 'Geplaatst');
            $lang_update_queries[] = PT_UpdateLangs($value, 'canceled', 'Geannuleerd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'packed', 'Ingepakt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'commission', 'Commissie');
            $lang_update_queries[] = PT_UpdateLangs($value, 'final_price', 'Uiteindelijke prijs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'link', 'Koppeling');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_commission', 'Sitecommissie');
            $lang_update_queries[] = PT_UpdateLangs($value, 'currently_unavailable.', 'Momenteel niet beschikbaar.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'write_review', 'Schrijf recensie');
            $lang_update_queries[] = PT_UpdateLangs($value, 'photos', 'Foto\'s');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_purchase', 'Geverifieerde aankoop');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events', 'Gebeurtenissen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_addresses', 'Mijn adressen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new', 'Voeg nieuw toe');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_address', 'verander adres');
            $lang_update_queries[] = PT_UpdateLangs($value, 'postcode___zip', 'Postcode / ZIP');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invitation_links', 'Uitnodigingsverbindingen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_links', 'Beschikbare links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generated_links', 'Gegenereerde links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'used_links', 'Gebruikte links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_link', 'Link genereren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invited_user', 'Uitgenodigde gebruiker');
            $lang_update_queries[] = PT_UpdateLangs($value, 'date', 'Datum');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copy', 'Kopiëren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copied', 'Gekopieerd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_wallet', 'Beschikbare portemonnee');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_up_wallet', 'Top-up portemonnee');
            $lang_update_queries[] = PT_UpdateLangs($value, 'hall_of_fame', 'Hall of fame');
            $lang_update_queries[] = PT_UpdateLangs($value, 'analytics', 'Analytiek');
            $lang_update_queries[] = PT_UpdateLangs($value, 'more_info', 'Meer informatie');
            $lang_update_queries[] = PT_UpdateLangs($value, 'listen_in_youtube', 'Luister in YouTube');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tagged_artists', 'Tagged artiesten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate', 'Doneren');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_other', 'Ander');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_clothes', 'Kleren');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_electronic', 'Elektronisch');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remove_from_cart', 'Verwijderen van winkelwagen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_to_cart', 'Voeg toe aan winkelkar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_cart_is_empty.', 'Uw winkelwagen is leeg.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_address', 'Verwijder uw adres');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_address_', 'Weet je zeker dat je dit adres wilt verwijderen?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_alert', 'Betalingswaarschuwing');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_', 'Weet je zeker dat je wilt betalen?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_product', 'Verwijder uw product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_product_', 'Weet je zeker dat je dit product wilt verwijderen?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_for_story', 'Betaal voor het verhaal');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_for_create_story_', 'Weet je zeker dat je wilt betalen voor het maken van verhaal?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_from_wallet', 'Betaal van portemonnee');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_buy_a_ticket_', 'Weet je zeker dat je een ticket wilt kopen?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave_event', 'Gebeurtenis');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_leave_this_event_', 'Weet je zeker dat je dit evenement wilt verlaten?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave', 'Vertrekken');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_event', 'Verwijder uw evenement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_event_', 'Weet je zeker dat je dit evenement wilt verwijderen?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get Verified, verkoop uw nummers, verkoop producten, maak evenementen en verkoop tickets, ontvang een speciaal uitziende profiel en wordt beroemd op ons platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get Verified, verkoop je nummers, maak evenementen en verkoop tickets, krijg een speciaal uitziende profiel en word beroemd op ons platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Ontvang geverifieerd, verkoop je songs, verkoop producten, krijg een speciaal uitziende profiel en word beroemd op ons platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get Verified, verkoop je nummers, krijg een speciaal uitziende profiel en word beroemd op ons platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_events_found', 'Geen evenementen gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event', 'Evenement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product', 'Product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate_button', 'Doneer knop');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_information', 'Mijn informatie');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_choose_which_information_you_would_like_to_download', 'Kies welke informatie u wilt downloaden.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_file', 'Het bestand genereren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_published_successfully', 'Uw evenement is succesvol gepubliceerd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_tickets_found', 'Geen tickets gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_tickets', 'Gekochte tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_updated_successfully', 'Uw evenement is succesvol bijgewerkt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_is_under_review', 'Uw product wordt beoordeeld');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_published_successfully', 'Uw product is succesvol gepubliceerd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_product', 'Bewerk product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_edited_successfully', 'Uw product is succesvol bewerkt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'guest', 'Gast');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket', 'Ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events_analytics', 'Evenementen Analytics');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id', 'ID kaart');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_artists', 'Tag artiesten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_other_artists_to_show_they_performed_together', 'Tag andere artiesten om te laten zien dat ze samen hebben uitgevoerd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_ticket', 'Ticket downloaden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_order_has_been_placed_successfully', 'Uw bestelling is succesvol geplaatst');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order', 'Volgorde');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sale_invoice', 'Verkoopfactuur');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_name', 'Naam van de verkoper');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_email', 'Verkoper e-mail');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invoice_to', 'Factuur aan');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_details', 'Betalingsdetails');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_due', 'Totaal verschuldigd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank_name', 'banknaam');
            $lang_update_queries[] = PT_UpdateLangs($value, 'item', 'Item');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_invoice', 'Factuur downloaden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'details', 'Details');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_products_found', 'Geen producten gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_reviews_found', 'Geen reviews gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_about_to_purchase_the_items__do_you_want_to_proceed_', 'Je staat op het punt om de items te kopen, wil je doorgaan?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request_a_refund', 'Vraag een terugbetaling');
            $lang_update_queries[] = PT_UpdateLangs($value, 'new_orders_has_been_placed', 'Er zijn nieuwe bestellingen geplaatst.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_status_has_been_changed', 'Uw bestelstatus is bijgewerkt.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_declined', 'Uw terugbetalingsverzoek is geweigerd.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_approved_your_money_added_to_your_wallet', 'Uw terugbetalingsverzoek is goedgekeurd, saldo opnieuw toegevoegd aan uw portemonnee.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'added_tracking_info', 'de bestelling bijgewerkt met trackinginformatie.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_approved', 'Uw product is goedgekeurd.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_your_event', 'voegde zich bij je evenement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bought_a_ticket', 'Kocht een ticket, je hebt een nieuwe verkoop!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'orders', 'Bestellingen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_purchased', 'U hebt dit artikel niet gekocht.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_not_found', 'Bestellen niet gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_status_wasn_t_set_to_delivered_within_60_days_from_the_order_date__it_will_be_automatically_be_sent_to__delivered_.', 'Als de bestelstatus niet is ingesteld op afgeleverd binnen 60 dagen na de besteldatum, wordt deze automatisch ingesteld op afgeleverd.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_wasn_t_actually_delivered__the_buyer_can_request_a_refund.', 'Als de bestelling niet daadwerkelijk is afgeleverd, kan de koper een terugbetaling aanvragen.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_request_is_under_review', 'Uw aanvraag wordt beoordeeld.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request', 'Verzoek');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_explain_the_reason', 'Leg de reden uit');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_products', 'Topproducten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___products_this_week', 'Best Selling Songs & Products deze week');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___albums_this_week', 'Best Selling Songs & Albums deze week');
            $lang_update_queries[] = PT_UpdateLangs($value, 'accepted_', 'Geaccepteerd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_wait__this_may_take_few_minutes.', 'Even geduld, dit kan enkele minuten duren.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'instead_of_uploading_a_song__you_can_easily_import_songs_using', 'In plaats van een nummer te uploaden, kunt u eenvoudig nummers importeren met');
            $lang_update_queries[] = PT_UpdateLangs($value, 'imported_a_new_song_', 'Importeerde een nieuw nummer,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_has_been_sent_successfully', 'Beoordeling is succesvol verzonden!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product_', 'nieuw product gemaakt,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_event_', 'Nieuwe gebeurtenis gemaakt,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_new_event_', 'toegetreden tot een nieuw evenement,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_a_ticket_', 'kocht een ticket,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_store', 'Mijn winkel');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_analytics', 'Bewaar Analytics');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products', 'Totale producten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_earned', 'totaal verdiend');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_commission', 'Totale commissie');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_sub_earned', 'Totaal verdiende sub');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_products', 'Meest verkochte producten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name_can_not_be_empty', 'Evenementnaam kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description_can_not_be_empty', 'Evenementbeschrijving kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date_can_not_be_empty', 'Startdatum kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_story', 'Maak een verhaal');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_related_song_can_not_be_empty', 'Productgerelateerd nummer kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_info', 'Product informatie');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_info', 'gebeurtenisinfo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_the_owner', 'Jij bent niet de eigenaar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_not_found', 'Gebeurtenis niet gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'this_event_is_free', 'Dit evenement is gratis');
            $lang_update_queries[] = PT_UpdateLangs($value, 'there_is_no_available_tickets', 'Er is geen beschikbare tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'card_is_empty', 'Kaart is leeg');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_can_not_be_empty', 'Adres kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id_can_not_be_empty', 'id kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location_can_not_be_empty', 'Evenementlocatie kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_time_can_not_be_empty', 'Starttijd kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date_can_not_be_empty', 'Einddatum kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_time_can_not_be_empty', 'Eindtijd kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone_can_not_be_empty', 'Timezone kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_image_can_not_be_empty', 'Evenementbeeld kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_title_can_not_be_empty', 'Producttitel kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_description_can_not_be_empty', 'Productbeschrijving kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_tags_can_not_be_empty', 'Producttags kunnen niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_price_can_not_be_empty', 'Productprijs kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_units_can_not_be_empty', 'Producteenheden kunnen niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_category_can_not_be_empty', 'Productcategorie kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_image_can_not_be_empty', 'Productafbeelding kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_not_found', 'product niet gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_not_found', 'Adres niet gevonden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_url_can_not_be_empty', 'Tracking-URL kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number_can_not_be_empty', 'Tracking-nummer kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_enter_a_valid_url', 'Voer een geldige URL in');
            $lang_update_queries[] = PT_UpdateLangs($value, 'rating_can_not_be_empty', 'beoordeling kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_can_not_be_empty', 'beoordeling kan niet leeg zijn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_who_can_see_the_story', 'Alsjeblieft wie het verhaal kan zien');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_image', 'Selecteer een verhaalbeeld');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_song', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_not_found_or_its_not_active', 'Verhaal niet gevonden of het is niet actief');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_your_songs', 'Verkoop je liedjes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_products', 'producten verkopen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_events_and_sell_tickets', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_more_songs', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_more_space', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_a_special_looking_profile_and_get_famous_on_our_platform_', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_was_purchased_in_sitename__date', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'track', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ticket', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'for', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_starts', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ends', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'video_duration_must_be_less_than_or_equal_10_seconds', 'Video Duur moet minder zijn dan of gelijk aan 10 seconden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_by', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_address', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_orders_found', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_to_purchase', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_video_will_be_converted_to_mp3_soon__you_ll_get_notified_once_imported', 'Uw video wordt binnenkort geconverteerd naar MP3, u ontvangt eenmaal eenmaal geïmporteerd');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_song_is_ready_to_view', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'Reviews', '');
        } else if ($value == 'french') {
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_added_successfully_', 'Votre adresse a été ajoutée avec succès!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_edited_successfully_', 'Votre adresse a été modifiée avec succès!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_name_must_be_between_5_32_', 'Le nom doit être compris entre 5/32');
            $lang_update_queries[] = PT_UpdateLangs($value, '_the_url_is_invalid._please_enter_a_valid_url_', 'L\'URL n\'est pas valide, veuillez entrer une URL valide.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image___video_', 'Le fichier multimédia n\'est pas valide, sélectionnez une image / vidéo valide.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image_', 'Le fichier multimédia n\'est pas valide, sélectionnez une image valide.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_audio_', 'Le fichier multimédia n\'est pas valide, veuillez sélectionner un fichier audio valide.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_too_many_login_attempts_please_try_again_later_', 'Trop de tentatives de connexion, veuillez réessayer plus tard.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_url_can_not_be_empty_', 'L\'URL ne peut pas être vide.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_address_can_not_be_empty_', 'L\'adresse ne peut pas être vide.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tickets_available_and_ticket_price_can_not_be_empty_', 'Billets Disponibilité et prix ne peuvent pas être vides.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_cover_can_not_be_empty_', 'La couverture d\'événement est requise.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_video_can_not_be_empty_', 'La vidéo d\'événement est requise.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_published_successfully_', 'Votre événement a été publié avec succès!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_updated_successfully_', 'Votre événement a été mis à jour avec succès!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_payment_successfully_done_', 'Paiement avec succès, merci!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_song_', 'Veuillez sélectionner une chanson.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_valid_image_', 'Veuillez sélectionner une image valide.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_published_successfully_', 'Votre produit a été publié avec succès!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_is_under_review_', 'Votre produit est soumis et sera revu bientôt.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_edited_successfully_', 'Votre produit a été édité avec succès!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_some_products_don_t_have_enough_of_units_', 'Certains de vos produits n\'ont pas assez d\'unités.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_wallet_', 'Vous n\'avez pas assez d\'équilibre dans votre portefeuille.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_top_up_your_wallet_', 'S\'il vous plaît recharger votre portefeuille');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_order_has_been_placed_successfully_', 'Votre commande a été enregistrée!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tracking_info_has_been_saved_successfully_', 'Les informations de suivi ont été enregistrées.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_review_has_been_sent_successfully_', 'L\'examen a été envoyé.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_request_is_under_review_', 'Votre demande est à l\'étude.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_published_successfully_', 'Votre histoire a été publiée avec succès!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_uploaded_successfully_to_publish_it_please_pay_', 'Votre histoire a été téléchargée, veuillez payer pour continuer.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_story_not_found_or_its_active_', 'Histoire non trouvée ou non active.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_money_please_top_up_your_wallet_', 'Vous n\'avez pas assez d\'équilibre dans votre portefeuille, veuillez recharger votre portefeuille.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_linkedin', 'Se connecter avec LinkedIn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_vkontakte', 'Connectez-vous avec vkontakte');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_instagram', 'Connectez-vous avec Instagram');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_qq', 'Connectez-vous avec QQ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_wechat', 'Connectez-vous avec wechat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_discord', 'Connectez-vous avec la discorde');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_mailru', 'Connectez-vous avec Mailru');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_items_found', 'Aucun élément trouvé.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_wallet', 'Vous n\'avez pas assez d\'équilibre dans votre portefeuille.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_top_up_your_wallet', 'S\'il vous plaît recharper votre portefeuille.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total', 'Le total');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new_address', 'Ajouter une nouvelle adresse');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_new_event', 'Créer un nouvel événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_events', 'Gérer les événements');
            $lang_update_queries[] = PT_UpdateLangs($value, 'browse_events', 'Parcourir les événements');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_events', 'Événements mariés');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_purchased_tickets', 'Voir les billets achetés');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name', 'Nom de l\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location', 'Lieu de l\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'online', 'En ligne');
            $lang_update_queries[] = PT_UpdateLangs($value, 'real_location', 'Emplacement réel');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_date', 'Date de début de l\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_time', 'Heure de début de l\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_date', 'Date de fin d\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_time', 'Temps de fin d\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone', 'Fuseau horaire');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_tickets', 'Vendre des tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tickets_available_total_tickets_available_for_this_event_', 'Billets disponibles (Total des billets disponibles pour cet événement)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_price', 'Prix ​​du billet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description', 'description de l\'évenement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_cover', 'Couverture d\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_video_trailer', 'Vidéo d\'événement / remorque');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_product', 'Créer un produit');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_products', 'Gérer les produits');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_item_units', 'Total des unités d\'article');
            $lang_update_queries[] = PT_UpdateLangs($value, 'related_to_song', 'Liée à la chanson');
            $lang_update_queries[] = PT_UpdateLangs($value, 'images', 'Images');
            $lang_update_queries[] = PT_UpdateLangs($value, 'who_can_see', 'Qui peut voir');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_my_followers_only', 'Montrer à mes adeptes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_all_users', 'Montrer à tous les utilisateurs (Promotion)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_image', 'Image de l\'histoire');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_song', 'Chant de téléchargement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'shipped', 'Expédié');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivered', 'Livré');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payments', 'Paiements');
            $lang_update_queries[] = PT_UpdateLangs($value, 'subtotal', 'Total');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund_money', 'Remboursement de l\'argent');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_details', 'Détails de suivi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_url', 'URL du site');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number', 'Numéro de suivi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivery_address', 'Adresse de livraison');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_orders_found', 'Aucune commande trouvée');
            $lang_update_queries[] = PT_UpdateLangs($value, 'products', 'Des produits');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_details', 'Voir les détails');
            $lang_update_queries[] = PT_UpdateLangs($value, 'stories', 'Histoires');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined', 'Rejoint');
            $lang_update_queries[] = PT_UpdateLangs($value, 'join', 'Rejoindre');
            $lang_update_queries[] = PT_UpdateLangs($value, 'buy_a_ticket', 'Acheter un ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_trailer', 'Voir la remorque');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_event', 'Édition');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date', 'Date de début');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date', 'Date de fin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_tickets', 'Billets disponibles');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_people', 'Rejoint les gens');
            $lang_update_queries[] = PT_UpdateLangs($value, 'location', 'Emplacement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_events', 'Événements total');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_joined_users', 'Total des utilisateurs joints');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_available_tickets', 'Total des billets disponibles');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_joined_events', 'Les événements les plus rejoints');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_events', 'Événements les plus vendus');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_events_found', 'Plus d\'événements trouvés');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_tickets_found', 'Plus de billets trouvés');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_products_found', 'Plus de produits trouvés');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_reviews_found', 'Pas de nouvelles critiques trouvées');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_successfully_done', 'Paiement effectué avec succès');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_song_', 'Êtes-vous sûr de vouloir payer pour acheter cette chanson?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_album_', 'Êtes-vous sûr de vouloir payer pour acheter cet album?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_upgrade_to_pro_', 'Êtes-vous sûr de vouloir passer à Pro?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_money_please_top_up_your_wallet', 'Vous n\'avez pas assez d\'argent s\'il vous plaît recharger votre portefeuille');
            $lang_update_queries[] = PT_UpdateLangs($value, 'interested', 'Intéressé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_views', 'Pas plus de vues');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_your_story_', 'Êtes-vous sûr de vouloir supprimer votre histoire?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_add_a_new_address', 'S\'il vous plaît ajouter une nouvelle adresse');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_address', 'Veuillez sélectionner l\'adresse');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund', 'Remboursement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_event', 'Créer un évènement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'checkout', 'Vérifier');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_orders', 'Commandes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_orders', 'Mes commandes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_request_found', 'Aucune demande trouvée');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_event', 'Supprimer l\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'cashfree', 'Sans argent');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paystack', 'Paysack');
            $lang_update_queries[] = PT_UpdateLangs($value, 'razorpay', 'Razorpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paysera', 'Paysera');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iyzipay', 'Iyzipay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payu', 'Paie');
            $lang_update_queries[] = PT_UpdateLangs($value, 'securionpay', 'SecurionPay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'authorize', 'Autoriser');
            $lang_update_queries[] = PT_UpdateLangs($value, 'placed', 'Mis');
            $lang_update_queries[] = PT_UpdateLangs($value, 'canceled', 'Annulé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'packed', 'Emballé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'commission', 'Commission');
            $lang_update_queries[] = PT_UpdateLangs($value, 'final_price', 'Prix ​​final');
            $lang_update_queries[] = PT_UpdateLangs($value, 'link', 'Relier');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_commission', 'Commission de site');
            $lang_update_queries[] = PT_UpdateLangs($value, 'currently_unavailable.', 'Actuellement indisponible.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'write_review', 'Ecrire une critique');
            $lang_update_queries[] = PT_UpdateLangs($value, 'photos', 'Photos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_purchase', 'Achat vérifié');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events', 'Événements');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_addresses', 'Mes adresses');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new', 'Ajouter de nouveau');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_address', 'Modifier l\'adresse');
            $lang_update_queries[] = PT_UpdateLangs($value, 'postcode___zip', 'Code postal / zip');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invitation_links', 'Liens d\'invitation');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_links', 'Liens disponibles');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generated_links', 'Liens générés');
            $lang_update_queries[] = PT_UpdateLangs($value, 'used_links', 'Liens d\'occasion');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_link', 'Générer un lien');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invited_user', 'Utilisateur invité');
            $lang_update_queries[] = PT_UpdateLangs($value, 'date', 'Date');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copy', 'Copie');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copied', 'Copié');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_wallet', 'Portefeuille disponible');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_up_wallet', 'Top up portefeuille');
            $lang_update_queries[] = PT_UpdateLangs($value, 'hall_of_fame', 'Temple de la renommée');
            $lang_update_queries[] = PT_UpdateLangs($value, 'analytics', 'Analytique');
            $lang_update_queries[] = PT_UpdateLangs($value, 'more_info', 'Plus d\'informations');
            $lang_update_queries[] = PT_UpdateLangs($value, 'listen_in_youtube', 'Écouter en youtube');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tagged_artists', 'Artistes marqués');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate', 'Faire un don');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_other', 'Autre');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_clothes', 'Vêtements');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_electronic', 'Électronique');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remove_from_cart', 'Supprimer du panier');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_to_cart', 'Ajouter au panier');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_cart_is_empty.', 'Votre panier est vide.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_address', 'Supprimer votre adresse');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_address_', 'Êtes-vous sûr de vouloir supprimer cette adresse?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_alert', 'Alerte de paiement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_', 'Êtes-vous sûr de vouloir payer?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_product', 'Supprimer votre produit');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_product_', 'Êtes-vous sûr de vouloir supprimer ce produit?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_for_story', 'Payer pour l\'histoire');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_for_create_story_', 'Êtes-vous sûr de vouloir payer pour créer une histoire?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_from_wallet', 'Payer du portefeuille');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_buy_a_ticket_', 'Êtes-vous sûr de vouloir acheter un billet?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave_event', 'Quitter l\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_leave_this_event_', 'Êtes-vous sûr de vouloir quitter cet événement?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave', 'Laisser');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_event', 'Supprimer votre événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_event_', 'Êtes-vous sûr de vouloir supprimer cet événement?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Obtenez vérifié, vendez vos chansons, vendez des produits, créez des événements et vendez des billets, obtenez un profil spécial et renommez sur notre plate-forme!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Obtenez vérifié, vendez vos chansons, créez des événements et vendez des billets, obtenez un profil spécial et vous familiariser sur notre plate-forme!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Vérifiez, vendez vos chansons, vendez des produits, obtenez un profil spécial et vous familiarisez sur notre plate-forme!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Obtenez vérifié, vendez vos chansons, obtenez un profil spécial et vous familiariser sur notre plate-forme!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_events_found', 'Aucun événement trouvé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event', 'Événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product', 'Produit');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate_button', 'Bouton DONE');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_information', 'Mon information');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_choose_which_information_you_would_like_to_download', 'Veuillez choisir les informations que vous souhaitez télécharger.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_file', 'Générer un fichier');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_published_successfully', 'Votre événement a été publié avec succès');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_tickets_found', 'Aucun billet trouvé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_tickets', 'Billets achetés');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_updated_successfully', 'Votre événement a été mis à jour avec succès');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_is_under_review', 'Votre produit est à l\'étude');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_published_successfully', 'Votre produit a été publié avec succès');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_product', 'Modifier le produit');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_edited_successfully', 'Votre produit a été édité avec succès');
            $lang_update_queries[] = PT_UpdateLangs($value, 'guest', 'Invité');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket', 'Billet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events_analytics', 'Analyse des événements');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id', 'identifiant');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_artists', 'Tag artistes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_other_artists_to_show_they_performed_together', 'Tag d\'autres artistes à montrer qu\'ils ont joué ensemble');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_ticket', 'Billet de téléchargement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_order_has_been_placed_successfully', 'Votre commande a été placée avec succès');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order', 'Commander');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sale_invoice', 'Facture de vente');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_name', 'Nom du Vendeur');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_email', 'Vendeur e-mail');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invoice_to', 'Facture à');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_details', 'Détails de paiement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_due', 'Total dû');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank_name', 'Nom de banque');
            $lang_update_queries[] = PT_UpdateLangs($value, 'item', 'Article');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_invoice', 'Télécharger la facture');
            $lang_update_queries[] = PT_UpdateLangs($value, 'details', 'Des détails');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_products_found', 'Aucun produit trouvé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_reviews_found', 'Aucun commentaire trouvé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_about_to_purchase_the_items__do_you_want_to_proceed_', 'Vous êtes sur le point d\'acheter les articles, voulez-vous continuer?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request_a_refund', 'Demande à être remboursé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'new_orders_has_been_placed', 'Les nouvelles commandes ont été placées.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_status_has_been_changed', 'Votre statut de commande a été mis à jour.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_declined', 'Votre demande de remboursement a été refusée.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_approved_your_money_added_to_your_wallet', 'Votre demande de remboursement a été approuvée, balance re-ajoutée à votre portefeuille.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'added_tracking_info', 'Mise à jour de la commande avec des informations de suivi.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_approved', 'Votre produit a été approuvé.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_your_event', 'rejoint votre événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bought_a_ticket', 'acheté un billet, vous avez une nouvelle vente!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'orders', 'Ordres');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_purchased', 'Vous n\'avez pas acheté cet article.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_not_found', 'Commande introuvable');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_status_wasn_t_set_to_delivered_within_60_days_from_the_order_date__it_will_be_automatically_be_sent_to__delivered_.', 'Si l\'état de la commande n\'était pas défini sur livré dans les 60 jours à compter de la date de la commande, il sera automatiquement défini sur livré.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_wasn_t_actually_delivered__the_buyer_can_request_a_refund.', 'Si la commande n\'a pas été réellement livrée, l\'acheteur peut demander un remboursement.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_request_is_under_review', 'Votre demande est à l\'étude.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request', 'Demander');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_explain_the_reason', 'S\'il vous plaît expliquer la raison');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_products', 'Top produits');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___products_this_week', 'Les meilleures chansons et produits de vente cette semaine');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___albums_this_week', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'accepted_', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_wait__this_may_take_few_minutes.', 'Veuillez patienter, cela peut prendre quelques minutes.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'instead_of_uploading_a_song__you_can_easily_import_songs_using', 'Au lieu de télécharger une chanson, vous pouvez facilement importer des chansons en utilisant');
            $lang_update_queries[] = PT_UpdateLangs($value, 'imported_a_new_song_', 'Importé une nouvelle chanson,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_has_been_sent_successfully', 'L\'avis a été envoyé avec succès!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product_', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_event_', 'créé nouvel événement,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_new_event_', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_a_ticket_', 'acheté un billet,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_store', 'Mon magasin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_analytics', 'Store Analytics');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_earned', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_commission', 'Commission totale');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_sub_earned', 'Total submergé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_products', 'Produits les plus vendus');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name_can_not_be_empty', 'Le nom de l\'événement ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description_can_not_be_empty', 'Description de l\'événement ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date_can_not_be_empty', 'La date de début ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_story', 'Créer une histoire');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_related_song_can_not_be_empty', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_info', 'Information sur le produit');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_info', 'Informations sur l\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_the_owner', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_not_found', 'Événement non trouvé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'this_event_is_free', 'Cet événement est gratuit');
            $lang_update_queries[] = PT_UpdateLangs($value, 'there_is_no_available_tickets', 'Il n\'y a pas de billets disponibles');
            $lang_update_queries[] = PT_UpdateLangs($value, 'card_is_empty', 'La carte est vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_can_not_be_empty', 'L\'adresse ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id_can_not_be_empty', 'id ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location_can_not_be_empty', 'L\'emplacement de l\'événement ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_time_can_not_be_empty', 'Heure de début ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date_can_not_be_empty', 'La date de fin ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_time_can_not_be_empty', 'L\'heure de fin ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone_can_not_be_empty', 'Le fuseau horaire ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_image_can_not_be_empty', 'L\'image d\'événement ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_title_can_not_be_empty', 'Le titre du produit ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_description_can_not_be_empty', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_tags_can_not_be_empty', 'Les balises de produit ne peuvent pas être vides');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_price_can_not_be_empty', 'Le prix du produit ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_units_can_not_be_empty', 'Les unités de produits ne peuvent pas être vides');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_category_can_not_be_empty', 'La catégorie de produit ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_image_can_not_be_empty', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_not_found', 'Produit non trouvé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_not_found', 'Adresse introuvable');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_url_can_not_be_empty', 'L\'URL de suivi ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number_can_not_be_empty', 'Le numéro de suivi ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_enter_a_valid_url', 'S\'il vous plaît entrer une URL valide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'rating_can_not_be_empty', 'la note ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_can_not_be_empty', 'la revue ne peut pas être vide');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_who_can_see_the_story', 'S\'il vous plaît qui peut voir l\'histoire');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_image', 'S\'il vous plaît sélectionnez une image de l\'histoire');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_song', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_not_found_or_its_not_active', 'Histoire non trouvée ou elle n\'est pas active');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified', 'Être vérifié');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_your_songs', 'Vendre vos chansons');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_products', 'vendez des produits');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_events_and_sell_tickets', 'Créer des événements et vendre des billets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_more_songs', 'Télécharger plus de chansons');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_more_space', 'obtenir plus d\'espace');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_a_special_looking_profile_and_get_famous_on_our_platform_', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_was_purchased_in_sitename__date', 'Le billet a été acheté dans {SITENAME}, {DATE}');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product', 'Nouveau produit créé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'track', 'Pister');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ticket', 'Billet d\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'for', 'Pour');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_starts', 'Événement commence');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ends', 'Se termine');
            $lang_update_queries[] = PT_UpdateLangs($value, 'video_duration_must_be_less_than_or_equal_10_seconds', 'La durée de la vidéo doit être inférieure ou égale 10 secondes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_by', 'Acheté par');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_address', 'Adresse d\'événement');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_orders_found', 'Plus de commandes trouvées');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_to_purchase', '');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_video_will_be_converted_to_mp3_soon__you_ll_get_notified_once_imported', 'Votre vidéo sera convertie en mp3 prochainement, vous serez notifié une fois importé');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_song_is_ready_to_view', 'Votre chanson est prête à voir.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'Reviews', 'Commentaires');
        } else if ($value == 'german') {
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_added_successfully_', 'Ihre Adresse wurde erfolgreich hinzugefügt!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_edited_successfully_', 'Ihre Adresse wurde erfolgreich bearbeitet!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_name_must_be_between_5_32_', 'Der Name muss zwischen 5/32 liegen');
            $lang_update_queries[] = PT_UpdateLangs($value, '_the_url_is_invalid._please_enter_a_valid_url_', 'Die URL ist ungültig, bitte geben Sie eine gültige URL ein.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image___video_', 'Die Mediendatei ist ungültig, bitte wählen Sie ein gültiges Bild / Video aus.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image_', 'Die Mediendatei ist ungültig, bitte wählen Sie ein gültiges Bild aus.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_audio_', 'Die Mediendatei ist ungültig, bitte wählen Sie eine gültige Audiodatei aus.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_too_many_login_attempts_please_try_again_later_', 'Zu viele Login-Versuche, bitte versuchen Sie es später erneut.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_url_can_not_be_empty_', 'URL kann nicht leer sein.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_address_can_not_be_empty_', 'Adresse kann nicht leer sein.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tickets_available_and_ticket_price_can_not_be_empty_', 'Tickets Verfügbarkeit und Preis kann nicht leer sein.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_cover_can_not_be_empty_', 'Ereignisdeckel ist erforderlich.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_video_can_not_be_empty_', 'Ereignisvideo ist erforderlich.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_published_successfully_', 'Ihre Veranstaltung wurde erfolgreich veröffentlicht!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_updated_successfully_', 'Ihre Veranstaltung wurde erfolgreich aktualisiert!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_payment_successfully_done_', 'Zahlung erfolgreich, danke!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_song_', 'Bitte wählen Sie ein Lied aus.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_valid_image_', 'Bitte wählen Sie ein gültiges Bild aus.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_published_successfully_', 'Ihr Produkt wurde erfolgreich veröffentlicht!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_is_under_review_', 'Ihr Produkt wird eingereicht und wird bald überprüft.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_edited_successfully_', 'Ihr Produkt wurde erfolgreich bearbeitet!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_some_products_don_t_have_enough_of_units_', 'Einige Ihrer Produkte haben nicht genügend Einheiten.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_wallet_', 'Sie haben nicht genug Gleichgewicht in Ihrer Brieftasche.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_top_up_your_wallet_', 'Bitte tippen Sie Ihre Brieftasche auf');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_order_has_been_placed_successfully_', 'Deine Bestellung wurde aufgenommen!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tracking_info_has_been_saved_successfully_', 'Tracking-Info wurde gespeichert.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_review_has_been_sent_successfully_', 'Überprüfung wurde gesendet.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_request_is_under_review_', 'Ihre Anfrage ist überprüft.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_published_successfully_', 'Ihre Geschichte wurde erfolgreich veröffentlicht!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_uploaded_successfully_to_publish_it_please_pay_', 'Ihre Geschichte wurde hochgeladen, bitte zahlen Sie, um fortzufahren.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_story_not_found_or_its_active_', 'Geschichte nicht gefunden oder nicht aktiv.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_money_please_top_up_your_wallet_', 'Sie haben nicht genug Gleichgewicht in Ihrer Brieftasche, bitte tippen Sie bitte Ihre Brieftasche auf.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_linkedin', 'Login mit LinkedIn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_vkontakte', 'Melden Sie sich mit VKONTAKTE an');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_instagram', 'Melden Sie sich mit Instagram an');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_qq', 'Login mit QQ.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_wechat', 'Login mit WECHAT');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_discord', 'Login mit der Zwietracht');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_mailru', 'Melden Sie sich mit MailRU an');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_items_found', 'Keine Elemente gefunden.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_wallet', 'Sie haben nicht genug Gleichgewicht in Ihrer Brieftasche.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_top_up_your_wallet', 'Bitte tippen Sie Ihre Brieftasche auf.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total', 'Gesamt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new_address', 'Neue Adresse hinzufügen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_new_event', 'Neue Ereignis erstellen.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_events', 'Events verwalten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'browse_events', 'Events durchsuchen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_events', 'Events beigetreten.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_purchased_tickets', 'Kaufgekaufte Tickets anzeigen.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name', 'Veranstaltungsname');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location', 'Veranstaltungsort');
            $lang_update_queries[] = PT_UpdateLangs($value, 'online', 'Online');
            $lang_update_queries[] = PT_UpdateLangs($value, 'real_location', 'Echter Ort.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_date', 'Event-Startdatum');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_time', 'Event-Startzeit.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_date', 'Ereignis-Enddatum');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_time', 'Ereignis-Endzeit.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone', 'Zeitzone');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_tickets', 'Tickets verkaufen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tickets_available_total_tickets_available_for_this_event_', 'Verfügbare Tickets (Gesamtkarten für diese Veranstaltung)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_price', 'Ticket Preis');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description', 'Eventbeschreibung');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_cover', 'Event Cover');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_video_trailer', 'Ereignisvideo / Anhänger');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_product', 'Produkt erstellen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_products', 'Produkte verwalten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_item_units', 'Gesamtstückeinheiten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'related_to_song', 'Im Zusammenhang mit Song.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'images', 'Bilde');
            $lang_update_queries[] = PT_UpdateLangs($value, 'who_can_see', 'Wer kann sehen?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_my_followers_only', 'Zeigen Sie meinen Anhängern');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_all_users', 'Alle Benutzer anzeigen (Promotion)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_image', 'Geschichten-Image');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_song', 'Song hochladen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'shipped', 'Geliefert');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivered', 'Geliefert');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payments', 'Zahlungen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'subtotal', 'Zwischensumme');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund_money', 'Rückerstattung von Geld');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_details', 'Verfolgungsdetails');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_url', 'Seiten-URL');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number', 'Auftragsnummer, Frachtnummer, Sendungscode');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivery_address', 'Lieferadresse');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_orders_found', 'Keine Bestellungen gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'products', 'Produkte');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_details', 'Details anzeigen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'stories', 'Geschichten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined', 'Trat bei');
            $lang_update_queries[] = PT_UpdateLangs($value, 'join', 'Verbinden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'buy_a_ticket', 'Kauf ein Ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_trailer', 'Anhänger anzeigen.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_event', 'Event bearbeiten.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date', 'Startdatum');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date', 'Endtermin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_tickets', 'Verfügbare Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_people', 'Menschen beigetreten.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'location', 'Standort');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_events', 'Gesamtereignisse');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_joined_users', 'Gesamtzahl beigetretenen Benutzern.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_available_tickets', 'Gesamt verfügbare Tickets.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_joined_events', 'Die meisten zusammengeschlossenen Ereignisse.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_events', 'Die meisten verkauften Events.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_events_found', 'Keine Ereignisse mehr gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_tickets_found', 'Keine Tickets mehr gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_products_found', 'Keine Produkte mehr gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_reviews_found', 'Keine mehr Bewertungen mehr gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_successfully_done', 'Zahlung erfolgreich erledigt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_song_', 'Möchten Sie sicher, dass Sie diesen Song kaufen möchten?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_album_', 'Möchten Sie sicher, dass Sie dieses Album kaufen möchten?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_upgrade_to_pro_', 'Möchten Sie sicher, dass Sie das Upgrade auf Pro aktualisieren möchten?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_money_please_top_up_your_wallet', 'Sie haben nicht genug Geld, bitte tippen Sie Ihre Brieftasche auf');
            $lang_update_queries[] = PT_UpdateLangs($value, 'interested', 'Interessiert');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_views', 'Keine Ansichten mehr.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_your_story_', 'Möchten Sie Ihre Geschichte sicher, dass Sie Ihre Geschichte löschen möchten?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_add_a_new_address', 'Bitte fügen Sie eine neue Adresse hinzu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_address', 'Bitte wählen Sie Adresse');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund', 'Erstattung');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_event', 'Event erstellen.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'checkout', 'Auschecken');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_orders', 'Aufträge aufbewahren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_orders', 'meine Bestellungen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_request_found', 'Keine Anfrage gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_event', 'Event löschen.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'cashfree', 'Barrierefrei');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paystack', 'PayStack');
            $lang_update_queries[] = PT_UpdateLangs($value, 'razorpay', 'Razorpay.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paysera', 'Paysera.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iyzipay', 'IYZIPAY.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payu', 'Payu.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'securionpay', 'SecurionPay.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'authorize', 'Autorisieren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'placed', 'Platziert');
            $lang_update_queries[] = PT_UpdateLangs($value, 'canceled', 'Abgesagt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'packed', 'Verpackt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'commission', 'Kommission');
            $lang_update_queries[] = PT_UpdateLangs($value, 'final_price', 'Endgültiger Preis');
            $lang_update_queries[] = PT_UpdateLangs($value, 'link', 'Verknüpfung');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_commission', 'Standortkommission');
            $lang_update_queries[] = PT_UpdateLangs($value, 'currently_unavailable.', 'Momentan nicht verfügbar.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'write_review', 'Bewertung schreiben');
            $lang_update_queries[] = PT_UpdateLangs($value, 'photos', 'Fotos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_purchase', 'Geprüfter Kauf.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events', 'Veranstaltungen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_addresses', 'Meine Adressen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new', 'Neue hinzufügen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_address', 'Adresse bearbeiten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'postcode___zip', 'Postleitzahl / Zip.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invitation_links', 'Einladungslinks');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_links', 'Verfügbare Links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generated_links', 'Erzeugte Links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'used_links', 'Gebrauchte Links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_link', 'Verbindung generieren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invited_user', 'Eingeladener Benutzer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'date', 'Datum');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copy', 'Kopieren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copied', 'Kopiert');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_wallet', 'Verfügbare Geldbörse');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_up_wallet', 'Wallet auffüllen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'hall_of_fame', 'Ruhmeshalle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'analytics', 'Analytik');
            $lang_update_queries[] = PT_UpdateLangs($value, 'more_info', 'Mehr Info');
            $lang_update_queries[] = PT_UpdateLangs($value, 'listen_in_youtube', 'Hören Sie auf YouTube an');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tagged_artists', 'Tagged Künstler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate', 'Spenden');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_other', 'Sonstiges');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_clothes', 'Kleider');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_electronic', 'Elektronisch');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remove_from_cart', 'Aus dem Warenkorb entfernen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_to_cart', 'In den Warenkorb legen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_cart_is_empty.', 'Ihr Warenkorb ist leer.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_address', 'Löschen Sie Ihre Adresse');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_address_', 'Möchten Sie diese Adresse sicher, dass Sie diese Adresse löschen möchten?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_alert', 'Zahlungsalarm');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_', 'Bist du sicher, dass du zahlen willst?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_product', 'Löschen Sie Ihr Produkt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_product_', 'Möchten Sie dieses Produkt sicher, dass Sie dieses Produkt löschen möchten?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_for_story', 'Für die Geschichte bezahlen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_for_create_story_', 'Möchten Sie sicher, dass Sie für die Schaffung von Geschichte bezahlen möchten?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_from_wallet', 'Bezahlen Sie von der Brieftasche');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_buy_a_ticket_', 'Möchten Sie sicher, dass Sie ein Ticket kaufen möchten?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave_event', 'Veranstaltung verlassen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_leave_this_event_', 'Sind Sie sicher, dass Sie dieses Ereignis verlassen möchten?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave', 'Verlassen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_event', 'Löschen Sie Ihre Veranstaltung');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_event_', 'Sind Sie sicher, dass Sie dieses Ereignis löschen möchten?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get nachgewiesen, verkaufen Sie Ihre Songs, verkaufen Sie Produkte, erstellen Sie Events und verkaufen Sie Tickets, erhalten Sie ein spezielles Profil und berühmt auf unserer Plattform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Überprüfen Sie, verifizieren Sie Ihre Songs, erstellen Sie Events und verkaufen Sie Tickets, erhalten Sie ein spezielles Profil und berühmt auf unserer Plattform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Überprüfen Sie, verkaufen Sie Ihre Songs, verkaufen Sie Produkte, erhalten Sie ein spezielles Profil und berühmt auf unserer Plattform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Verifizieren, verkaufen Sie Ihre Songs, holen Sie sich ein spezielles Profil aus und berühmt auf unserer Plattform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_events_found', 'Keine Ereignisse gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event', 'Vorfall');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product', 'Produkt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate_button', 'Knopf spenden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_information', 'Meine Information');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_choose_which_information_you_would_like_to_download', 'Bitte wählen Sie, welche Informationen Sie herunterladen möchten.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_file', 'Datei generieren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_published_successfully', 'Ihre Veranstaltung wurde erfolgreich veröffentlicht');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_tickets_found', 'Keine Tickets gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_tickets', 'Gekaufte Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_updated_successfully', 'Ihre Veranstaltung wurde erfolgreich aktualisiert');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_is_under_review', 'Ihr Produkt ist überprüft');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_published_successfully', 'Ihr Produkt wurde erfolgreich veröffentlicht');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_product', 'Produkt bearbeiten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_edited_successfully', 'Ihr Produkt wurde erfolgreich bearbeitet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'guest', 'Gast');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket', 'Fahrkarte');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events_analytics', 'Events Analytics.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id', 'ICH WÜRDE');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_artists', 'Tag Künstler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_other_artists_to_show_they_performed_together', 'Andere Künstler, die zeigen, dass sie zusammengearbeitet haben');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_ticket', 'Ticket herunterladen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_order_has_been_placed_successfully', 'Ihre Bestellung wurde erfolgreich platziert');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order', 'Befehl');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sale_invoice', 'Verkauf Rechnung.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_name', 'Name des Verkäufers');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_email', 'Verkäufer-E-Mail.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invoice_to', 'Rechnung an');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_details', 'Zahlungsdetails');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_due', 'Insgesamt fällig.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank_name', 'Bank Name');
            $lang_update_queries[] = PT_UpdateLangs($value, 'item', 'Artikel');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_invoice', 'Download Rechnung');
            $lang_update_queries[] = PT_UpdateLangs($value, 'details', 'Einzelheiten');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_products_found', 'Keine Produkte gefunden.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_reviews_found', 'Keine Bewertungen gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_about_to_purchase_the_items__do_you_want_to_proceed_', 'Sie können die Artikel kaufen, möchten Sie fortfahren?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request_a_refund', 'Eine Rückerstattung anfordern');
            $lang_update_queries[] = PT_UpdateLangs($value, 'new_orders_has_been_placed', 'Neuaufträge wurden platziert.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_status_has_been_changed', 'Ihr Bestellstatus wurde aktualisiert.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_declined', 'Ihre Erstattungsanfrage wurde abgelehnt.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_approved_your_money_added_to_your_wallet', 'Ihre Erstattungsanfrage wurde genehmigt, Balance wird Ihrer Brieftasche erneut hinzugefügt.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'added_tracking_info', 'die Bestellung mit Tracking-Informationen aktualisiert.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_approved', 'Ihr Produkt wurde genehmigt.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_your_event', 'schloss sich Ihrer Veranstaltung an');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bought_a_ticket', 'Kaufte ein Ticket, du hast einen neuen Verkauf!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'orders', 'Aufträge');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_purchased', 'Sie haben diesen Artikel nicht gekauft.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_not_found', 'Bestellen nicht gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_status_wasn_t_set_to_delivered_within_60_days_from_the_order_date__it_will_be_automatically_be_sent_to__delivered_.', 'Wenn der Bestellstatus nicht innerhalb von 60 Tagen ab dem Bestelldatum angeliefert wurde, wird es automatisch aufgeliefert.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_wasn_t_actually_delivered__the_buyer_can_request_a_refund.', 'Wenn die Bestellung nicht tatsächlich geliefert wurde, kann der Käufer eine Rückerstattung anfordern.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_request_is_under_review', 'Ihre Anfrage ist überprüft.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request', 'Anfrage');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_explain_the_reason', 'Bitte erkläre den Grund');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_products', 'Top-Produkte');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___products_this_week', 'Bestseller Songs & Produkte Diese Woche');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___albums_this_week', 'Bestseller Songs & Alben Diese Woche');
            $lang_update_queries[] = PT_UpdateLangs($value, 'accepted_', 'Akzeptiert');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_wait__this_may_take_few_minutes.', 'Bitte warten, dies kann nur wenige Minuten dauern.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'instead_of_uploading_a_song__you_can_easily_import_songs_using', 'Anstatt ein Lied hochzuladen, können Sie Songs problemlos importieren');
            $lang_update_queries[] = PT_UpdateLangs($value, 'imported_a_new_song_', 'Ein neues Lied importiert,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_has_been_sent_successfully', 'Die Überprüfung wurde erfolgreich gesendet!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product_', 'neues Produkt erstellt,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_event_', 'Neues Ereignis erstellt,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_new_event_', 'mit dem neuen Ereignis,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_a_ticket_', 'ein Ticket gekauft,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_store', 'Mein Laden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_analytics', 'Analytik speichern');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products', 'Gesamtprodukte');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_earned', 'insgesamt verdient');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_commission', 'Total Commission.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_sub_earned', 'Total sub verdient');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_products', 'Die meisten verkauften Produkte');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name_can_not_be_empty', 'Ereignisname kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description_can_not_be_empty', 'Ereignisbeschreibung kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date_can_not_be_empty', 'Startdatum kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_story', 'Geschichte erstellen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_related_song_can_not_be_empty', 'Produktbedingter Song kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_info', 'Produktinformation');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_info', 'Ereignisinfo.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_the_owner', 'Du bist nicht der Besitzer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_not_found', 'Ereignis nicht gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'this_event_is_free', 'Diese Veranstaltung ist kostenlos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'there_is_no_available_tickets', 'Es gibt keine verfügbaren Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'card_is_empty', 'Karte ist leer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_can_not_be_empty', 'Adresse kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id_can_not_be_empty', 'ID kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location_can_not_be_empty', 'Der Ereignisstandort kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_time_can_not_be_empty', 'Startzeit kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date_can_not_be_empty', 'Enddatum kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_time_can_not_be_empty', 'Endzeit kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone_can_not_be_empty', 'Zeitzone kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_image_can_not_be_empty', 'Ereignisbild kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_title_can_not_be_empty', 'Der Produkttitel kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_description_can_not_be_empty', 'Produktbeschreibung kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_tags_can_not_be_empty', 'Produkt-Tags können nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_price_can_not_be_empty', 'Produktpreis kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_units_can_not_be_empty', 'Produkteinheiten können nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_category_can_not_be_empty', 'Produktkategorie kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_image_can_not_be_empty', 'Produktbild kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_not_found', 'Produkt nicht gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_not_found', 'Adresse nicht gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_url_can_not_be_empty', 'Tracking-URL kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number_can_not_be_empty', 'Die Tracking-Nummer kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_enter_a_valid_url', 'Bitte geben Sie eine gültige URL ein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'rating_can_not_be_empty', 'Bewertung kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_can_not_be_empty', 'Überprüfung kann nicht leer sein');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_who_can_see_the_story', 'Bitte, wer kann die Geschichte sehen?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_image', 'Bitte wählen Sie ein Story-Image aus');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_song', 'Bitte wählen Sie ein Story-Song aus');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_not_found_or_its_not_active', 'Geschichte nicht gefunden oder nicht aktiv');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified', 'Verifiziert werden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_your_songs', 'Verkaufen Sie Ihre Songs.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_products', 'Produkte verkaufen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_events_and_sell_tickets', 'Erstellen Sie Events und verkaufen Sie Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_more_songs', 'Laden Sie mehr Songs hoch');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_more_space', 'mehr Platz bekommen.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Holen Sie sich ein spezielles Profil und berühmt auf unserer Plattform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_was_purchased_in_sitename__date', 'Das Ticket wurde in {SITENAME} erworben, {DATE}');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product', 'Neues Produkt erstellt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'track', 'Spur');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ticket', 'Event-Ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'for', 'Zum');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_starts', 'Veranstaltung beginnt.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ends', 'Ereignis endet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'video_duration_must_be_less_than_or_equal_10_seconds', 'Die Videodauer muss weniger als 10 Sekunden betragen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_by', 'Erworben von');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_address', 'Ereignisadresse');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_orders_found', 'Keine Bestellungen mehr gefunden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_to_purchase', 'Melden Sie sich beim Kauf an');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_video_will_be_converted_to_mp3_soon__you_ll_get_notified_once_imported', 'Ihr Video wird in Kürze in MP3 umgewandelt, Sie werden einmal mit dem Import benachrichtigt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_song_is_ready_to_view', 'Ihr Lied ist bereit zu sehen.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'Reviews', 'Rezensionen');
        } else if ($value == 'russian') {
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_added_successfully_', 'Ваш адрес был успешно добавлен!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_edited_successfully_', 'Ваш адрес был успешно отредактирован!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_name_must_be_between_5_32_', 'Имя должно быть между 5/32');
            $lang_update_queries[] = PT_UpdateLangs($value, '_the_url_is_invalid._please_enter_a_valid_url_', 'URL-адрес недействителен, введите действительный URL.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image___video_', 'Медиа-файл недействителен, выберите действительное изображение / видео.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image_', 'Media File недействителен, выберите действительное изображение.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_audio_', 'Media File недействителен, выберите действительный аудиофайл.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_too_many_login_attempts_please_try_again_later_', 'Слишком много попыток входа в систему, пожалуйста, попробуйте позже.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_url_can_not_be_empty_', 'URL не может быть пустым.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_address_can_not_be_empty_', 'Адрес не может быть пустым.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tickets_available_and_ticket_price_can_not_be_empty_', 'Наличие и цены на билеты не могут быть пустыми.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_cover_can_not_be_empty_', 'Обложка событий требуется.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_video_can_not_be_empty_', 'Требуется видео события.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_published_successfully_', 'Ваше мероприятие было опубликовано успешно!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_updated_successfully_', 'Ваше мероприятие было успешно обновлено!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_payment_successfully_done_', 'Оплата успешно, спасибо!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_song_', 'Пожалуйста, выберите песню.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_valid_image_', 'Пожалуйста, выберите действительное изображение.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_published_successfully_', 'Ваш продукт был успешно опубликован!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_is_under_review_', 'Ваш продукт отправлен и будет рассмотрен в ближайшее время.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_edited_successfully_', 'Ваш продукт был успешно отредактирован!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_some_products_don_t_have_enough_of_units_', 'Некоторые из ваших продуктов не имеют достаточностей.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_wallet_', 'У вас недостаточно баланса в вашем кошельке.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_top_up_your_wallet_', 'Пожалуйста, пополните свой кошелек');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_order_has_been_placed_successfully_', 'Ваш заказ был размещен!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tracking_info_has_been_saved_successfully_', 'Информация о отслеживании была сохранена.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_review_has_been_sent_successfully_', 'Обзор был отправлен.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_request_is_under_review_', 'Ваш запрос находится под контролем.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_published_successfully_', 'Ваша история была успешно опубликована!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_uploaded_successfully_to_publish_it_please_pay_', 'Ваша история была загружена, пожалуйста, оплатите, чтобы продолжить.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_story_not_found_or_its_active_', 'История не найдена или не активна.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_money_please_top_up_your_wallet_', 'У вас не хватает баланса в вашем кошельке, пожалуйста, пополните свой кошелек.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_linkedin', 'Войти с linkedin.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_vkontakte', 'Войти с Вконтакте');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_instagram', 'Войти с Instagram.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_qq', 'Войти с помощью QQ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_wechat', 'Войти с wechat.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_discord', 'Войти с раздортом');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_mailru', 'Войти с MailRu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_items_found', 'Ничего не найдено.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_wallet', 'У вас недостаточно баланса в вашем кошельке.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_top_up_your_wallet', 'Пожалуйста, пополните свой кошелек.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total', 'Общий');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new_address', 'Добавьте новый адрес');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_new_event', 'Создать новое событие');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_events', 'Управление событиями');
            $lang_update_queries[] = PT_UpdateLangs($value, 'browse_events', 'Просматривать события');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_events', 'Присоединились события');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_purchased_tickets', 'Просмотр приобретенных билетов');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name', 'Название события');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location', 'Мероприятие Местонахождение');
            $lang_update_queries[] = PT_UpdateLangs($value, 'online', 'онлайн');
            $lang_update_queries[] = PT_UpdateLangs($value, 'real_location', 'Реальное расположение');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_date', 'Дата начала события');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_time', 'Время начала события');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_date', 'Дата окончания событий');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_time', 'Время окончания событий');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone', 'Часовой пояс');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_tickets', 'Продавать билеты');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tickets_available_total_tickets_available_for_this_event_', 'Доступны билеты (общие билеты доступны для этого мероприятия)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_price', 'Стоимость билета');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description', 'Описание события');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_cover', 'Обложка события');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_video_trailer', 'Видео события / трейлер');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_product', 'Создать продукт');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_products', 'Управление продуктами');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_item_units', 'Всего единиц товара');
            $lang_update_queries[] = PT_UpdateLangs($value, 'related_to_song', 'Связанные с песней');
            $lang_update_queries[] = PT_UpdateLangs($value, 'images', 'Изображений');
            $lang_update_queries[] = PT_UpdateLangs($value, 'who_can_see', 'Кто может видеть');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_my_followers_only', 'Показать моим последователям');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_all_users', 'Показать всем пользователям (продвижение)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_image', 'История истории');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_song', 'Загрузить песню');
            $lang_update_queries[] = PT_UpdateLangs($value, 'shipped', 'Отправленный');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivered', 'Доставленный');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payments', 'Платежи');
            $lang_update_queries[] = PT_UpdateLangs($value, 'subtotal', 'Промежуточный итог');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund_money', 'Возвращать деньги');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_details', 'Отслеживание деталей');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_url', 'Адрес сайта');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number', 'Номер Отслеживания');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivery_address', 'Адрес доставки');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_orders_found', 'Заказы не найдены');
            $lang_update_queries[] = PT_UpdateLangs($value, 'products', 'Продукты');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_details', 'Посмотреть детали');
            $lang_update_queries[] = PT_UpdateLangs($value, 'stories', 'Истории');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined', 'Присоединился');
            $lang_update_queries[] = PT_UpdateLangs($value, 'join', 'Присоединиться');
            $lang_update_queries[] = PT_UpdateLangs($value, 'buy_a_ticket', 'Купить билет');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_trailer', 'Просмотр трейлера');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_event', 'Редактировать событие');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date', 'Дата начала');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date', 'Дата окончания');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_tickets', 'Доступные билеты');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_people', 'Присоединились к людям');
            $lang_update_queries[] = PT_UpdateLangs($value, 'location', 'Место нахождения');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_events', 'Всего мероприятия');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_joined_users', 'Всего соединенных пользователей');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_available_tickets', 'Всего доступных билетов');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_joined_events', 'Большинство присоединившихся событий');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_events', 'Самые проданные события');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_events_found', 'Больше не найдено событий');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_tickets_found', 'Больше не найдено билетов');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_products_found', 'Больше не найдено продуктов');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_reviews_found', 'Больше не найдено больше отзывов');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_successfully_done', 'Оплата успешно сделана');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_song_', 'Вы уверены, что хотите заплатить, чтобы купить эту песню?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_album_', 'Вы уверены, что хотите заплатить, чтобы купить этот альбом?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_upgrade_to_pro_', 'Вы уверены, что хотите обновить до Pro?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_money_please_top_up_your_wallet', 'У вас не хватает денег, пожалуйста, пополните свой кошелек');
            $lang_update_queries[] = PT_UpdateLangs($value, 'interested', 'Заинтересовать');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_views', 'Нет больше просмотров');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_your_story_', 'Вы уверены, что хотите удалить свою историю?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_add_a_new_address', 'Пожалуйста, добавьте новый адрес');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_address', 'Пожалуйста, выберите адрес');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund', 'Возвращать деньги');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_event', 'Создать событие');
            $lang_update_queries[] = PT_UpdateLangs($value, 'checkout', 'Проверить');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_orders', 'Заказы на хранилище');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_orders', 'мои заказы');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_request_found', 'Ни один запрос не обнаружен');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_event', 'Удалить событие');
            $lang_update_queries[] = PT_UpdateLangs($value, 'cashfree', 'Кашельство');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paystack', 'Платный плата');
            $lang_update_queries[] = PT_UpdateLangs($value, 'razorpay', 'Razorpay.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paysera', 'Paysera');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iyzipay', 'Iyzipay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payu', 'Окупаемость');
            $lang_update_queries[] = PT_UpdateLangs($value, 'securionpay', 'Securionpay.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'authorize', 'Разрешать');
            $lang_update_queries[] = PT_UpdateLangs($value, 'placed', 'Размещены');
            $lang_update_queries[] = PT_UpdateLangs($value, 'canceled', 'Отменил');
            $lang_update_queries[] = PT_UpdateLangs($value, 'packed', 'Упакованный');
            $lang_update_queries[] = PT_UpdateLangs($value, 'commission', 'Комиссия');
            $lang_update_queries[] = PT_UpdateLangs($value, 'final_price', 'Окончательная цена');
            $lang_update_queries[] = PT_UpdateLangs($value, 'link', 'Ссылка');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_commission', 'Комиссия сайта');
            $lang_update_queries[] = PT_UpdateLangs($value, 'currently_unavailable.', 'В настоящее время недоступен.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'write_review', 'Написать обзор');
            $lang_update_queries[] = PT_UpdateLangs($value, 'photos', 'Фото');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_purchase', 'Проверенная покупка');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events', 'События');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_addresses', 'Мои адреса');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new', 'Добавить новое');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_address', 'Редактировать адрес');
            $lang_update_queries[] = PT_UpdateLangs($value, 'postcode___zip', 'Почтовый индекс / Почтовый индекс');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invitation_links', 'Пригласительные ссылки');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_links', 'Доступные ссылки');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generated_links', 'Сгенерированные ссылки');
            $lang_update_queries[] = PT_UpdateLangs($value, 'used_links', 'Используемые ссылки');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_link', 'Генерировать ссылку');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invited_user', 'Приглашенный пользователь');
            $lang_update_queries[] = PT_UpdateLangs($value, 'date', 'Дата');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copy', 'Скопировать');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copied', 'Скопирован');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_wallet', 'Доступный кошелек');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_up_wallet', 'Пополнить кошелек');
            $lang_update_queries[] = PT_UpdateLangs($value, 'hall_of_fame', 'Зал славы');
            $lang_update_queries[] = PT_UpdateLangs($value, 'analytics', 'Аналитика');
            $lang_update_queries[] = PT_UpdateLangs($value, 'more_info', 'Больше информации');
            $lang_update_queries[] = PT_UpdateLangs($value, 'listen_in_youtube', 'Слушай в YouTube');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tagged_artists', 'Тегистых художников');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate', 'Пожертвовать');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_other', 'Другой');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_clothes', 'Одежда');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_electronic', 'Электронный');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remove_from_cart', 'Удалить из корзины');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_to_cart', 'Добавить в корзину');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_cart_is_empty.', 'Ваша корзина пуста.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_address', 'Удалить свой адрес');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_address_', 'Вы уверены, что хотите удалить этот адрес?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_alert', 'Оплата');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_', 'Вы уверены, что хотите заплатить?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_product', 'Удалить ваш продукт');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_product_', 'Вы уверены, что хотите удалить этот продукт?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_for_story', 'Платить за историю');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_for_create_story_', 'Вы уверены, что хотите заплатить за создать историю?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_from_wallet', 'Платить из кошелька');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_buy_a_ticket_', 'Вы уверены, что хотите купить билет?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave_event', 'Оставьте событие');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_leave_this_event_', 'Вы уверены, что хотите покинуть это событие?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave', 'Оставлять');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_event', 'Удалить свое мероприятие');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_event_', 'Вы уверены, что хотите удалить это событие?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Проверено, продавайте свои песни, продавайте продукты, создайте мероприятия и продажи билетов, получите специальный профиль и получите знаменитую на нашей платформе!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Получите проверенные, продавайте свои песни, создайте события и продажи билетов, получите специальный профиль и получите знаменитую на нашей платформе!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Получите проверку, продайте свои песни, продайте продукты, получите специальный поиск профиль и получите знаменитую на нашей платформе!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Получите проверку, продайте свои песни, получите специальный поиск профиль и получите знаменитую нашу платформу!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_events_found', 'События не найдены');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event', 'Мероприятие');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product', 'Продукт');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate_button', 'Пожертвовать кнопку');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_information', 'Моя информация');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_choose_which_information_you_would_like_to_download', 'Пожалуйста, выберите, какую информацию вы хотите скачать.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_file', 'Генерировать файл');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_published_successfully', 'Ваше мероприятие было успешно опубликовано');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_tickets_found', 'Билеты не найдено');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_tickets', 'Купленные билеты');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_updated_successfully', 'Ваше мероприятие успешно обновлено');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_is_under_review', 'Ваш продукт находится под рассмотрением');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_published_successfully', 'Ваш продукт успешно опубликован');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_product', 'Редактировать продукт');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_edited_successfully', 'Ваш продукт успешно отредактирован');
            $lang_update_queries[] = PT_UpdateLangs($value, 'guest', 'Гость');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket', 'Проездной билет');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events_analytics', 'События аналитики');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id', 'Я БЫ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_artists', 'Теги художников');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_other_artists_to_show_they_performed_together', 'Течь других артистов, чтобы показать, что они выступали вместе');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_ticket', 'Скачать билет');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_order_has_been_placed_successfully', 'Ваш заказ был успешно размещен');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order', 'порядок');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sale_invoice', 'Продажа счет');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_name', 'Название продавца');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_email', 'По электронной почте продавца');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invoice_to', 'Счет-фактуру');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_details', 'Детали оплаты');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_due', 'Всего должное');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank_name', 'название банка');
            $lang_update_queries[] = PT_UpdateLangs($value, 'item', 'Элемент');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_invoice', 'Скачать счет');
            $lang_update_queries[] = PT_UpdateLangs($value, 'details', 'Подробности');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_products_found', 'Никаких продуктов не найдена');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_reviews_found', 'Отзывов не найдено');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_about_to_purchase_the_items__do_you_want_to_proceed_', 'Вы собираетесь приобрести предметы, вы хотите продолжить?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request_a_refund', 'Запросить возврат');
            $lang_update_queries[] = PT_UpdateLangs($value, 'new_orders_has_been_placed', 'Новые заказы были размещены.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_status_has_been_changed', 'Ваш статус заказа был обновлен.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_declined', 'Ваш запрос на возврат был отклонен.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_approved_your_money_added_to_your_wallet', 'Ваш запрос на возврат был одобрен, баланс повторно добавляется в ваш кошелек.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'added_tracking_info', 'Обновил заказ с информацией отслеживания.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_approved', 'Ваш продукт был одобрен.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_your_event', 'присоединился к вашему событию');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bought_a_ticket', 'Купил билет, у вас есть новая распродажа!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'orders', 'Заказывает');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_purchased', 'Вы не купили этот товар.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_not_found', 'Не найден');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_status_wasn_t_set_to_delivered_within_60_days_from_the_order_date__it_will_be_automatically_be_sent_to__delivered_.', 'Если статус заказа не был установлен для доставки в течение 60 дней с даты заказа, он будет автоматически установлен для доставки.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_wasn_t_actually_delivered__the_buyer_can_request_a_refund.', 'Если заказ на самом деле не был доставлен, покупатель может запросить возмещение.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_request_is_under_review', 'Ваш запрос находится под контролем.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request', 'Запрос');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_explain_the_reason', 'Пожалуйста, объясните причину');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_products', 'Лучшие товары');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___products_this_week', 'Лучшие продажи песни и продукты на этой неделе');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___albums_this_week', 'Лучшие продажи песен и альбомы на этой неделе');
            $lang_update_queries[] = PT_UpdateLangs($value, 'accepted_', 'Принято');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_wait__this_may_take_few_minutes.', 'Пожалуйста, подождите, это может занять несколько минут.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'instead_of_uploading_a_song__you_can_easily_import_songs_using', 'Вместо того, чтобы загрузить песню, вы можете легко импортировать песни, используя');
            $lang_update_queries[] = PT_UpdateLangs($value, 'imported_a_new_song_', 'Импортировал новую песню,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_has_been_sent_successfully', 'Обзор был успешно отправлен!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product_', 'Создан новый продукт,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_event_', 'Создано новое событие,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_new_event_', 'присоединился к новому событию,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_a_ticket_', 'купил билет,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_store', 'Мой магазин');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_analytics', 'Хранить аналитику');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products', 'Общая продукция');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_earned', 'Всего заработало');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_commission', 'Общая комиссия');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_sub_earned', 'Общая подразделение');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_products', 'Самые проданные продукты');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name_can_not_be_empty', 'Имя события не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description_can_not_be_empty', 'Описание события не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date_can_not_be_empty', 'Дата начала не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_story', 'Создать историю');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_related_song_can_not_be_empty', 'Соглашенная продукция песня не может быть пустой');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_info', 'Информация о продукте');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_info', 'Информация о событии');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_the_owner', 'Вы не являетесь владельцем');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_not_found', 'Событие не найдено');
            $lang_update_queries[] = PT_UpdateLangs($value, 'this_event_is_free', 'Это событие бесплатно');
            $lang_update_queries[] = PT_UpdateLangs($value, 'there_is_no_available_tickets', 'Нет доступных билетов');
            $lang_update_queries[] = PT_UpdateLangs($value, 'card_is_empty', 'Карта пуста');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_can_not_be_empty', 'Адрес не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id_can_not_be_empty', 'Идентификатор не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location_can_not_be_empty', 'Место события не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_time_can_not_be_empty', 'Время начала не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date_can_not_be_empty', 'Дата окончания не может быть пустой');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_time_can_not_be_empty', 'Время окончания не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone_can_not_be_empty', 'Часовой пояс не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_image_can_not_be_empty', 'Изображение события не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_title_can_not_be_empty', 'Название продукта не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_description_can_not_be_empty', 'Описание продукта не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_tags_can_not_be_empty', 'Теги продукта не могут быть пустыми');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_price_can_not_be_empty', 'Цена продукта не может быть пустой');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_units_can_not_be_empty', 'Единицы продукта не могут быть пустыми');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_category_can_not_be_empty', 'Категория продукта не может быть пустой');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_image_can_not_be_empty', 'Изображение продукта не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_not_found', 'Продукт не найден');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_not_found', 'Адрес не найден');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_url_can_not_be_empty', 'УРЛ отслеживания не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number_can_not_be_empty', 'Номер отслеживания не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_enter_a_valid_url', 'Пожалуйста, введите корректный адрес');
            $lang_update_queries[] = PT_UpdateLangs($value, 'rating_can_not_be_empty', 'Рейтинг не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_can_not_be_empty', 'Обзор не может быть пустым');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_who_can_see_the_story', 'Пожалуйста, кто может видеть историю');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_image', 'Пожалуйста, выберите историю изображения');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_song', 'Пожалуйста, выберите историю песни');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_not_found_or_its_not_active', 'История не найдена или не активна');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified', 'Пройти проверку');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_your_songs', 'Продайте свои песни');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_products', 'продавать продукты');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_events_and_sell_tickets', 'Создание мероприятий и продажи билетов');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_more_songs', 'Загрузить больше песен');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_more_space', 'получить больше места');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Получите специальный поиск профиль и получите знаменитую на нашей платформе!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_was_purchased_in_sitename__date', 'Билет был куплен в {SITENAME}, {DATE}');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product', 'Создан новый продукт');
            $lang_update_queries[] = PT_UpdateLangs($value, 'track', 'Отслеживать');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ticket', 'Билет событий');
            $lang_update_queries[] = PT_UpdateLangs($value, 'for', 'Для');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_starts', 'Событие начинается');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ends', 'Событие заканчивается');
            $lang_update_queries[] = PT_UpdateLangs($value, 'video_duration_must_be_less_than_or_equal_10_seconds', 'Продолжительность видео должна быть меньше или равна 10 секунд');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_by', 'Куплен');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_address', 'Адрес событий');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_orders_found', 'Больше не найдено заказов');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_to_purchase', 'Войти в покупку');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_video_will_be_converted_to_mp3_soon__you_ll_get_notified_once_imported', 'Ваше видео будет преобразовано в MP3 в ближайшее время, вы получите уведомление после импорта');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_song_is_ready_to_view', 'Ваша песня готова к просмотру.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'Reviews', 'Отзывы');
        } else if ($value == 'spanish') {
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_added_successfully_', '¡Su dirección se ha agregado con éxito!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_edited_successfully_', '¡Su dirección ha sido editada con éxito!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_name_must_be_between_5_32_', 'El nombre debe estar entre 5/32');
            $lang_update_queries[] = PT_UpdateLangs($value, '_the_url_is_invalid._please_enter_a_valid_url_', 'La URL no es válida, ingrese una URL válida.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image___video_', 'El archivo multimedia no es válido, seleccione una imagen / video válida.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image_', 'El archivo de medios no es válido, seleccione una imagen válida.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_audio_', 'El archivo de medios no es válido, seleccione un archivo de audio válido.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_too_many_login_attempts_please_try_again_later_', 'Demasiados intentos de inicio de sesión, inténtalo de nuevo más tarde.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_url_can_not_be_empty_', 'URL no puede estar vacío.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_address_can_not_be_empty_', 'La dirección no puede estar vacía.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tickets_available_and_ticket_price_can_not_be_empty_', 'Las boletos La disponibilidad y el precio no pueden estar vacíos.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_cover_can_not_be_empty_', 'Se requiere la cubierta de eventos.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_video_can_not_be_empty_', 'Se requiere video de evento.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_published_successfully_', 'Su evento ha sido publicado con éxito!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_updated_successfully_', '¡Su evento ha sido actualizado con éxito!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_payment_successfully_done_', 'Pago con éxito, gracias!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_song_', 'Por favor, seleccione una canción.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_valid_image_', 'Por favor, seleccione una imagen válida.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_published_successfully_', '¡Su producto ha sido publicado con éxito!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_is_under_review_', 'Su producto se envía y será revisado pronto.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_edited_successfully_', '¡Su producto ha sido editado con éxito!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_some_products_don_t_have_enough_of_units_', 'Algunos de sus productos no tienen suficientes unidades.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_wallet_', 'No tienes suficiente equilibrio en tu billetera.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_top_up_your_wallet_', 'Por favor, recargue su billetera');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_order_has_been_placed_successfully_', '¡Su orden ha sido puesta!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tracking_info_has_been_saved_successfully_', 'La información de seguimiento se ha guardado.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_review_has_been_sent_successfully_', 'Revisión ha sido enviada.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_request_is_under_review_', 'Su solicitud está bajo revisión.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_published_successfully_', 'Tu historia ha sido publicada con éxito!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_uploaded_successfully_to_publish_it_please_pay_', 'Su historia ha sido cargada, por favor pague para continuar.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_story_not_found_or_its_active_', 'Historia no encontrada o no activa.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_money_please_top_up_your_wallet_', 'No tiene suficiente equilibrio en su billetera, por favor recargue su billetera.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_linkedin', 'Ingresar con LinkedIn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_vkontakte', 'Inicia sesión con vkontakte');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_instagram', 'Inicia sesión con Instagram');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_qq', 'Inicia sesión con QQ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_wechat', 'Inicia sesión con WeChat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_discord', 'Iniciar sesión con la discordia');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_mailru', 'Inicia sesión con mailru');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_items_found', 'No se encontraron artículos.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_wallet', 'No tienes suficiente equilibrio en tu billetera.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_top_up_your_wallet', 'Por favor, recargue su billetera.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total', 'Total');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new_address', 'Agregar nueva dirección');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_new_event', 'Crear nuevo evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_events', 'Gestionar eventos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'browse_events', 'Navegar eventos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_events', 'Eventos unidos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_purchased_tickets', 'Ver boletos comprados');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name', 'Nombre del evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location', 'Lugar del evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'online', 'En línea');
            $lang_update_queries[] = PT_UpdateLangs($value, 'real_location', 'Ubicación real');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_date', 'Fecha de inicio del evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_time', 'Tiempo de inicio del evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_date', 'Fecha de finalización del evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_time', 'Evento final de tiempo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone', 'Zona horaria');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_tickets', 'Venta de entradas');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tickets_available_total_tickets_available_for_this_event_', 'Entradas disponibles (Total Boletos disponibles para este evento)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_price', 'Precio de venta');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description', 'descripción del evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_cover', 'Portada de eventos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_video_trailer', 'Evento Video / Trailer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_product', 'Crear producto');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_products', 'Gestionar productos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_item_units', 'Unidades de elementos totales');
            $lang_update_queries[] = PT_UpdateLangs($value, 'related_to_song', 'Relacionado con la canción');
            $lang_update_queries[] = PT_UpdateLangs($value, 'images', 'Imágenes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'who_can_see', 'Quien puede ver');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_my_followers_only', 'Mostrar a mis seguidores');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_all_users', 'Mostrar a todos los usuarios (promoción)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_image', 'Imagen de la historia');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_song', 'Cargar canción');
            $lang_update_queries[] = PT_UpdateLangs($value, 'shipped', 'Enviado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivered', 'Entregado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payments', 'Pagos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'subtotal', 'Total parcial');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund_money', 'Reembolsar dinero');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_details', 'Detalles de seguimiento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_url', 'Sitio URL');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number', 'El número de rastreo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivery_address', 'Dirección de entrega');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_orders_found', 'No se han encontrado pedidos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'products', 'Productos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_details', 'Ver detalles');
            $lang_update_queries[] = PT_UpdateLangs($value, 'stories', 'Cuentos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined', 'Unido');
            $lang_update_queries[] = PT_UpdateLangs($value, 'join', 'Entrar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'buy_a_ticket', 'Comprar un boleto');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_trailer', 'Vista del remolque');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_event', 'Editar evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date', 'Fecha de inicio');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date', 'Fecha final');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_tickets', 'Entradas disponibles');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_people', 'Se unió a las personas');
            $lang_update_queries[] = PT_UpdateLangs($value, 'location', 'Localización');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_events', 'Eventos totales');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_joined_users', 'Usuarios totales unidos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_available_tickets', 'Tickets totales disponibles');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_joined_events', 'Eventos más unidos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_events', 'Los eventos más vendidos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_events_found', 'No más eventos encontrados');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_tickets_found', 'No más boletos encontrados');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_products_found', 'No se encontraron más productos.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_reviews_found', 'No se encontraron más opiniones.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_successfully_done', 'Pago realizado con éxito');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_song_', '¿Estás seguro de que quieres pagar para comprar esta canción?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_album_', '¿Estás seguro de que quieres pagar para comprar este álbum?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_upgrade_to_pro_', '¿Estás seguro de que quieres actualizar a Pro?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_money_please_top_up_your_wallet', 'No tienes suficiente dinero por favor recarga tu billetera');
            $lang_update_queries[] = PT_UpdateLangs($value, 'interested', 'Interesado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_views', 'No más vistas');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_your_story_', '¿Estás seguro de que quieres eliminar tu historia?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_add_a_new_address', 'Por favor agregue una nueva dirección');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_address', 'Por favor seleccione la dirección');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund', 'Reembolso');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_event', 'Crear evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'checkout', 'Verificar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_orders', 'Órdenes de la tienda');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_orders', 'Mis ordenes');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_request_found', 'No se encontró ninguna solicitud');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_event', 'Eliminar evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'cashfree', 'CashFree');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paystack', 'Paystack');
            $lang_update_queries[] = PT_UpdateLangs($value, 'razorpay', 'Razorpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paysera', 'Paysera');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iyzipay', 'IYZIPAY');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payu', 'Payu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'securionpay', 'PAYO DE SECURION');
            $lang_update_queries[] = PT_UpdateLangs($value, 'authorize', 'Autorizar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'placed', 'Metido');
            $lang_update_queries[] = PT_UpdateLangs($value, 'canceled', 'Cancelado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'packed', 'Lleno');
            $lang_update_queries[] = PT_UpdateLangs($value, 'commission', 'Comisión');
            $lang_update_queries[] = PT_UpdateLangs($value, 'final_price', 'Precio final');
            $lang_update_queries[] = PT_UpdateLangs($value, 'link', 'Enlace');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_commission', 'Comisión del sitio');
            $lang_update_queries[] = PT_UpdateLangs($value, 'currently_unavailable.', 'Actualmente no disponible.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'write_review', 'Escribir un comentario');
            $lang_update_queries[] = PT_UpdateLangs($value, 'photos', 'Fotografías');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_purchase', 'Compra verificada');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events', 'Eventos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_addresses', 'Mis direcciones');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new', 'Añadir nuevo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_address', 'Editar dirección');
            $lang_update_queries[] = PT_UpdateLangs($value, 'postcode___zip', 'Código postal / ZIP');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invitation_links', 'Enlaces de invitación');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_links', 'Enlaces disponibles');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generated_links', 'Enlaces generados');
            $lang_update_queries[] = PT_UpdateLangs($value, 'used_links', 'Enlaces usados');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_link', 'Generar enlace');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invited_user', 'Usuario invitado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'date', 'Fecha');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copy', 'Dupdo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copied', 'Copiado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_wallet', 'Billetera disponible');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_up_wallet', 'Tapa de la cartera');
            $lang_update_queries[] = PT_UpdateLangs($value, 'hall_of_fame', 'Salón de la Fama');
            $lang_update_queries[] = PT_UpdateLangs($value, 'analytics', 'Analítica');
            $lang_update_queries[] = PT_UpdateLangs($value, 'more_info', 'Más información');
            $lang_update_queries[] = PT_UpdateLangs($value, 'listen_in_youtube', 'Escucha en YouTube');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tagged_artists', 'Artistas etiquetados');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate', 'Donar');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_other', 'Otro');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_clothes', 'Ropa');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_electronic', 'Electrónico');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remove_from_cart', 'Quitar del carrito');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_to_cart', 'Añadir al carrito');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_cart_is_empty.', 'Tu carrito esta vacío.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_address', 'Elimina tu dirección');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_address_', '¿Está seguro de que desea eliminar esta dirección?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_alert', 'Alerta de pago');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_', '¿Estás seguro de que quieres pagar?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_product', 'Elimina tu producto');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_product_', '¿Está seguro de que desea eliminar este producto?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_for_story', 'Pagar por la historia');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_for_create_story_', '¿Estás seguro de que quieres pagar por crear historia?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_from_wallet', 'Pagar de billetera');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_buy_a_ticket_', '¿Estás seguro de que quieres comprar un boleto?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave_event', 'Dejar evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_leave_this_event_', '¿Estás seguro de que quieres dejar este evento?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave', 'Dejar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_event', 'Elimina tu evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_event_', '¿Estás seguro de que quieres eliminar este evento?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', '¡Verifiede, vende sus canciones, venda productos, cree eventos y venda boletos, obtenga un perfil de aspecto especial y hágase famoso en nuestra plataforma!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', '¡Consigue su verificación, vende sus canciones, cree eventos y venda boletos, obtenga un perfil de aspecto especial y hágase famoso en nuestra plataforma!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___get_a_special_looking_profile_and_get_famous_on_our_platform_', '¡Verifiede, venda sus canciones, venda productos, obtenga un perfil de aspecto especial y se vuelva famosa en nuestra plataforma!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___get_a_special_looking_profile_and_get_famous_on_our_platform_', '¡Verifiede, vende sus canciones, obtenga un perfil de aspecto especial y hágase famoso en nuestra plataforma!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_events_found', 'No se han encontrado eventos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event', 'Evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product', 'Producto');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate_button', 'Botón de donar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_information', 'Mi informacion');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_choose_which_information_you_would_like_to_download', 'Por favor, elija qué información desea descargar.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_file', 'Generar archivo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_published_successfully', 'Su evento ha sido publicado con éxito.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_tickets_found', 'No se han encontrado boletos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_tickets', 'Entradas compradas');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_updated_successfully', 'Su evento ha sido actualizado con éxito.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_is_under_review', 'Tu producto esta bajo revisión');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_published_successfully', 'Su producto ha sido publicado con éxito.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_product', 'Editar producto');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_edited_successfully', 'Su producto ha sido editado con éxito.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'guest', 'Huésped');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket', 'Billete');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events_analytics', 'Análisis de eventos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id', 'IDENTIFICACIÓN');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_artists', 'Artistas de etiqueta');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_other_artists_to_show_they_performed_together', 'Etiquete otros artistas para mostrar que se desempeñaron juntos.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_ticket', 'Descargar el boleto');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_order_has_been_placed_successfully', 'Su pedido ha sido realizado con éxito.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order', 'Pedido');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sale_invoice', 'Factura de venta');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_name', 'Nombre del vendedor');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_email', 'Email del vendedor');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invoice_to', 'Factura a');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_details', 'Detalles del pago');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_due', 'Total de vencimiento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank_name', 'Nombre del banco');
            $lang_update_queries[] = PT_UpdateLangs($value, 'item', 'Artículo');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_invoice', 'Descargar factura');
            $lang_update_queries[] = PT_UpdateLangs($value, 'details', 'Detalles');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_products_found', 'No se han encontrado productos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_reviews_found', 'No se encontraron opiniones');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_about_to_purchase_the_items__do_you_want_to_proceed_', 'Está a punto de comprar los artículos, ¿desea continuar?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request_a_refund', 'Solicitar un reembolso');
            $lang_update_queries[] = PT_UpdateLangs($value, 'new_orders_has_been_placed', 'Se han colocado nuevos pedidos.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_status_has_been_changed', 'Su estado de pedido ha sido actualizado.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_declined', 'Su solicitud de reembolso ha sido rechazada.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_approved_your_money_added_to_your_wallet', 'Su solicitud de reembolso ha sido aprobada, el saldo se ha vuelto a agregar a su billetera.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'added_tracking_info', 'Actualizado el pedido con información de seguimiento.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_approved', 'Su producto ha sido aprobado.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_your_event', 'se unió a su evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bought_a_ticket', '¡Compró un boleto, tienes una nueva venta!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'orders', 'Pedidos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_purchased', 'No compró este artículo.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_not_found', 'Orden no encontrado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_status_wasn_t_set_to_delivered_within_60_days_from_the_order_date__it_will_be_automatically_be_sent_to__delivered_.', 'Si el estado del pedido no se configuró para entregar dentro de 60 días a partir de la fecha del pedido, se establecerá automáticamente en entregarse.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_wasn_t_actually_delivered__the_buyer_can_request_a_refund.', 'Si el pedido no se entregó en realidad, el comprador puede solicitar un reembolso.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_request_is_under_review', 'Su solicitud está bajo revisión.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request', 'Solicitud');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_explain_the_reason', 'Por favor explique la razón');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_products', 'Top Products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___products_this_week', 'Canciones y productos más vendidos esta semana');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___albums_this_week', 'Canciones y álbumes más vendidos esta semana');
            $lang_update_queries[] = PT_UpdateLangs($value, 'accepted_', 'Aceptado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_wait__this_may_take_few_minutes.', 'Por favor, espere, esto puede tardar unos minutos.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'instead_of_uploading_a_song__you_can_easily_import_songs_using', 'En lugar de cargar una canción, puede importar fácilmente las canciones usando');
            $lang_update_queries[] = PT_UpdateLangs($value, 'imported_a_new_song_', 'Importó una nueva canción,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_has_been_sent_successfully', '¡La revisión ha sido enviada con éxito!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product_', 'Nuevo producto creado,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_event_', 'creado nuevo evento,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_new_event_', 'un nuevo evento,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_a_ticket_', 'compró un boleto,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_store', 'Mi tienda');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_analytics', 'Análisis de la tienda');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products', 'Productos totales');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_earned', 'Total de Ganancias');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_commission', 'Comisión total');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_sub_earned', 'Total subvendado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_products', 'Productos más vendidos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name_can_not_be_empty', 'El nombre del evento no puede estar vacío');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description_can_not_be_empty', 'La descripción del evento no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date_can_not_be_empty', 'La fecha de inicio no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_story', 'Crear historia');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_related_song_can_not_be_empty', 'La canción relacionada con el producto no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_info', 'Información del producto');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_info', 'Información del evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_the_owner', 'Tu no eres el dueño');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_not_found', 'Evento no encontrado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'this_event_is_free', 'Este evento es gratis.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'there_is_no_available_tickets', 'No hay boletos disponibles.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'card_is_empty', 'La tarjeta esta vacia');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_can_not_be_empty', 'La dirección no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id_can_not_be_empty', 'La identificación no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location_can_not_be_empty', 'La ubicación del evento no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_time_can_not_be_empty', 'La hora de inicio no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date_can_not_be_empty', 'La fecha de finalización no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_time_can_not_be_empty', 'El tiempo final no puede estar vacío');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone_can_not_be_empty', 'TimeZone no puede estar vacío');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_image_can_not_be_empty', 'La imagen del evento no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_title_can_not_be_empty', 'El título del producto no puede estar vacío');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_description_can_not_be_empty', 'La descripción del producto no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_tags_can_not_be_empty', 'Las etiquetas del producto no pueden estar vacías.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_price_can_not_be_empty', 'El precio del producto no puede estar vacío');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_units_can_not_be_empty', 'Las unidades de productos no pueden estar vacías.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_category_can_not_be_empty', 'La categoría de producto no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_image_can_not_be_empty', 'La imagen del producto no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_not_found', 'Producto no encontrado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_not_found', 'Dirección no encontrada');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_url_can_not_be_empty', 'La URL de seguimiento no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number_can_not_be_empty', 'El número de seguimiento no puede estar vacío');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_enter_a_valid_url', 'Por favor introduzca un URL válido');
            $lang_update_queries[] = PT_UpdateLangs($value, 'rating_can_not_be_empty', 'la calificación no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_can_not_be_empty', 'La revisión no puede estar vacía');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_who_can_see_the_story', 'Por favor, quien puede ver la historia');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_image', 'Por favor, seleccione una imagen de historia');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_song', 'Por favor seleccione una canción de historia');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_not_found_or_its_not_active', 'La historia no se encuentra o no está activa.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified', 'Verifícate');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_your_songs', 'Vende tus canciones');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_products', 'Vender productos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_events_and_sell_tickets', 'Crear eventos y vender boletos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_more_songs', 'Sube más canciones');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_more_space', 'obtener más espacio');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_a_special_looking_profile_and_get_famous_on_our_platform_', '¡Obtén un perfil especial y hazte famoso en nuestra plataforma!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_was_purchased_in_sitename__date', 'El boleto fue comprado en {SITENAME}, {DATE}');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product', 'Creado nuevo producto');
            $lang_update_queries[] = PT_UpdateLangs($value, 'track', 'Pista');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ticket', 'Billete de eventos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'for', 'Para');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_starts', 'Evento comienza');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ends', 'Evento termina');
            $lang_update_queries[] = PT_UpdateLangs($value, 'video_duration_must_be_less_than_or_equal_10_seconds', 'La duración del video debe ser menor o igual de 10 segundos.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_by', 'Comprado por');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_address', 'Dirección del evento');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_orders_found', 'No se han encontrado más pedidos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_to_purchase', 'Inicia sesión para comprar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_video_will_be_converted_to_mp3_soon__you_ll_get_notified_once_imported', 'Su video se convertirá a MP3 pronto, recibirá notificado una vez importado');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_song_is_ready_to_view', 'Tu canción está lista para ver.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'Reviews', 'Comentarios');
        } else if ($value == 'turkish') {
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_added_successfully_', 'Adresiniz başarıyla eklendi!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_edited_successfully_', 'Adresiniz başarıyla düzenlendi!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_name_must_be_between_5_32_', 'ADI 5/32 arasında olmalıdır');
            $lang_update_queries[] = PT_UpdateLangs($value, '_the_url_is_invalid._please_enter_a_valid_url_', 'URL geçersiz, lütfen geçerli bir URL girin.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image___video_', 'Medya dosyası geçersiz, lütfen geçerli bir resim / video seçin.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image_', 'Medya dosyası geçersiz, lütfen geçerli bir görüntü seçin.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_audio_', 'Medya dosyası geçersiz, lütfen geçerli bir ses dosyası seçin.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_too_many_login_attempts_please_try_again_later_', 'Çok fazla giriş denemesi, lütfen daha sonra tekrar deneyin.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_url_can_not_be_empty_', 'URL boş olamaz.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_address_can_not_be_empty_', 'Adres boş olamaz.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tickets_available_and_ticket_price_can_not_be_empty_', 'Biletler kullanılabilirliği ve fiyat boş olamaz.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_cover_can_not_be_empty_', 'Olay kapağı gereklidir.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_video_can_not_be_empty_', 'Olay videosu gereklidir.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_published_successfully_', 'Etkinliğiniz başarıyla yayınlandı!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_updated_successfully_', 'Etkinliğiniz başarıyla güncellendi!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_payment_successfully_done_', 'Ödeme başarıyla, teşekkür ederim!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_song_', 'Lütfen bir şarkı seçin.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_valid_image_', 'Lütfen geçerli bir görüntü seçin.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_published_successfully_', 'Ürününüz başarılı bir şekilde yayınlandı!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_is_under_review_', 'Ürününüz gönderilir ve yakında gözden geçirilecektir.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_edited_successfully_', 'Ürününüz başarıyla düzenlendi!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_some_products_don_t_have_enough_of_units_', 'Ürünlerinizin bazılarının yeterli birim yok.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_wallet_', 'Cüzdanında yeterince dengeniz yok.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_top_up_your_wallet_', 'Lütfen cüzdanını doldur');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_order_has_been_placed_successfully_', 'Siparişiniz alındı!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tracking_info_has_been_saved_successfully_', 'İzleme bilgisi kaydedildi.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_review_has_been_sent_successfully_', 'İnceleme gönderildi.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_request_is_under_review_', 'İsteğiniz inceleme altında.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_published_successfully_', 'Hikayen başarılı bir şekilde yayınlandı!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_uploaded_successfully_to_publish_it_please_pay_', 'Hikayen yüklendi, lütfen devam etmek için ödeme yapın.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_story_not_found_or_its_active_', 'Hikaye bulunamadı ya da aktif değil.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_money_please_top_up_your_wallet_', 'Cüzdanınızda yeterli dengeniz yok, lütfen cüzdanınızı doldurun.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_linkedin', 'Linkedln ile giriş yap');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_vkontakte', 'VKontakte ile giriş yapın');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_instagram', 'Instagram ile giriş yapmak');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_qq', 'QQ ile giriş yapın');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_wechat', 'Wechat ile giriş yapın');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_discord', 'Uyumlulukla giriş yapın');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_mailru', 'Mailru ile giriş yapın');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_items_found', 'Hiç bir öğe bulunamadı.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_wallet', 'Cüzdanında yeterince dengeniz yok.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_top_up_your_wallet', 'Lütfen cüzdanınızı doldurun.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total', 'Toplam');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new_address', 'Yeni adres ekleyin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_new_event', 'Yeni Etkinlik Oluştur');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_events', 'Olayları yönet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'browse_events', 'Etkinliklere göz atın');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_events', 'Katıldı Etkinlikler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_purchased_tickets', 'Satın alınan biletleri görüntüle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name', 'Etkinlik adı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location', 'Olay yeri');
            $lang_update_queries[] = PT_UpdateLangs($value, 'online', 'İnternet üzerinden');
            $lang_update_queries[] = PT_UpdateLangs($value, 'real_location', 'Gerçek yer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_date', 'Olay Başlangıç ​​Tarihi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_time', 'Olay başlangıç ​​zamanı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_date', 'Etkinlik Sonu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_time', 'Etkinlik Sonu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone', 'Saat dilimi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_tickets', 'Bilet satmak');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tickets_available_total_tickets_available_for_this_event_', 'Biletler mevcut (bu etkinlik için mevcut toplam bilet)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_price', 'Bilet fiyatı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description', 'Etkinlik Açıklaması');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_cover', 'Olay kapağı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_video_trailer', 'Olay Video / Treyler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_product', 'Ürün oluştur');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_products', 'Ürünleri yönet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_item_units', 'Toplam Ürün Birimleri');
            $lang_update_queries[] = PT_UpdateLangs($value, 'related_to_song', 'Şarkı ile ilgili');
            $lang_update_queries[] = PT_UpdateLangs($value, 'images', 'Görüntüler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'who_can_see', 'Kim görebilir');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_my_followers_only', 'Takipçilerime göster');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_all_users', 'Tüm kullanıcılara göster (Promosyon)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_image', 'Hikaye resmi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_song', 'Şarkı yükle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'shipped', 'Sevk edilen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivered', 'Teslim edilmiş');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payments', 'Ödeme');
            $lang_update_queries[] = PT_UpdateLangs($value, 'subtotal', 'ara toplam');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund_money', 'Para iadesi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_details', 'Yürüyüş detayları');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_url', 'Site URL\'si');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number', 'Takip numarası');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivery_address', 'Teslimat adresi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_orders_found', 'sipariş bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'products', 'Ürün:% s');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_details', 'Detayları göster');
            $lang_update_queries[] = PT_UpdateLangs($value, 'stories', 'Öyküler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined', 'Katılmış');
            $lang_update_queries[] = PT_UpdateLangs($value, 'join', 'Katılmak');
            $lang_update_queries[] = PT_UpdateLangs($value, 'buy_a_ticket', 'Bilet satın al');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_trailer', 'Römork görmek');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_event', 'Etkinliği düzenle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date', 'Başlangıç ​​tarihi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date', 'Bitiş tarihi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_tickets', 'Mevcut Biletler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_people', 'Katıldı insanlar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'location', 'Konum');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_events', 'Toplam olaylar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_joined_users', 'Toplam Katılan Kullanıcılar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_available_tickets', 'Toplam Mevcut Biletler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_joined_events', 'En Çok Katılan Etkinlikler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_events', 'En çok satılan etkinlikler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_events_found', 'Artık etkinlik bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_tickets_found', 'Daha fazla bilet bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_products_found', 'Daha fazla ürün bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_reviews_found', 'Daha fazla yorum bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_successfully_done', 'Ödeme başarıyla yapıldı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_song_', 'Bu şarkıyı satın almak için ödemek istediğinize emin misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_album_', 'Bu albümü satın almak için ödemek istediğinize emin misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_upgrade_to_pro_', 'Pro\'ya yükseltmek istediğinize emin misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_money_please_top_up_your_wallet', 'Yeterince para yok, lütfen cüzdanını doldur');
            $lang_update_queries[] = PT_UpdateLangs($value, 'interested', 'Ilgilenen');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_views', 'Daha fazla görünüm yok');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_your_story_', 'Hikayeni silmek istediğinize emin misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_add_a_new_address', 'Lütfen yeni bir adres ekle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_address', 'Lütfen adresi seçin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund', 'Geri ödeme');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_event', 'Etkinlik oluşturmak');
            $lang_update_queries[] = PT_UpdateLangs($value, 'checkout', 'Ödeme');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_orders', 'Mağaza siparişleri');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_orders', 'Siparişlerim');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_request_found', 'İstek bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_event', 'Etkinliği sil');
            $lang_update_queries[] = PT_UpdateLangs($value, 'cashfree', 'Cashfree');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paystack', 'Paystack');
            $lang_update_queries[] = PT_UpdateLangs($value, 'razorpay', 'Razörpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paysera', 'Paysera');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iyzipay', 'İyzipay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payu', 'Ötesi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'securionpay', 'Securionpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'authorize', 'Yetki vermek');
            $lang_update_queries[] = PT_UpdateLangs($value, 'placed', 'Yerleştirilmiş');
            $lang_update_queries[] = PT_UpdateLangs($value, 'canceled', 'İptal edildi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'packed', 'Paketlenmiş');
            $lang_update_queries[] = PT_UpdateLangs($value, 'commission', 'komisyon');
            $lang_update_queries[] = PT_UpdateLangs($value, 'final_price', 'Son fiyat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'link', 'Bağlantı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_commission', 'Site Komisyonu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'currently_unavailable.', 'Şu anda kullanılamıyor.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'write_review', 'Yorum yaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'photos', 'Fotoğraflar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_purchase', 'Doğrulanmış Satınalma');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events', 'Olaylar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_addresses', 'Adresim');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new', 'Yeni ekle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_address', 'Adresi düzelt');
            $lang_update_queries[] = PT_UpdateLangs($value, 'postcode___zip', 'Posta kodu / zip');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invitation_links', 'Davetiye bağlantıları');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_links', 'Mevcut bağlantılar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generated_links', 'Oluşturulan bağlantılar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'used_links', 'Kullanılan linkler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_link', 'Bağlantı Oluştur');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invited_user', 'Davetli kullanıcı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'date', 'Tarih');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copy', 'Kopya');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copied', 'Kopyalandı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_wallet', 'Mevcut cüzdan');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_up_wallet', 'Topla cüzdan');
            $lang_update_queries[] = PT_UpdateLangs($value, 'hall_of_fame', 'Onur listesi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'analytics', 'Analitik');
            $lang_update_queries[] = PT_UpdateLangs($value, 'more_info', 'Daha fazla bilgi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'listen_in_youtube', 'YouTube\'da dinleyin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tagged_artists', 'Tagged sanatçılar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate', 'Bağış yapmak');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_other', 'Başka');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_clothes', 'Çamaşırlar');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_electronic', 'Elektronik');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remove_from_cart', 'Arabadan çıkar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_to_cart', 'Sepete ekle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_cart_is_empty.', 'Sepetiniz boş.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_address', 'Adresinizi Sil');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_address_', 'Bu adresi silmek istediğinize emin misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_alert', 'Ödeme uyarısı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_', 'Ödemek istediğinize emin misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_product', 'Ürününüzü silin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_product_', 'Bu ürünü silmek istediğinize emin misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_for_story', 'Hikaye için ödeme yapmak');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_for_create_story_', 'Hikaye oluşturmak için ödemek istediğinize emin misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_from_wallet', 'Cüzdandan ödeme');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_buy_a_ticket_', 'Bir bilet satın almak istediğinize emin misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave_event', 'Olay bırakmak');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_leave_this_event_', 'etkinlikten ayrılmak istediğinize emin misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave', 'Terk etmek');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_event', 'Etkinliğinizi Sil');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_event_', 'Bu etkinliği silmek istediğinizden emin misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Doğrulanmış, şarkılarınızı satmak, ürünleri satmak, etkinlikler oluşturmak ve bilet satmak, özel bir profil almak ve platformumuzla ünlü almak!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Doğrulanmış, şarkılarınızı satın, etkinlikler oluşturun ve bilet satmak, özel görünümlü bir profil alın ve platformumuzda ünlü olun!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Doğrulanmış, şarkılarınızı satmak, ürünleri satmak, özel görünümlü bir profil alın ve platformumuzda ünlü olun!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Doğrulanmış, şarkılarınızı satar, özel bir görünüm kazanın ve platformumuzda ünlü olun!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_events_found', 'Etkinlik bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event', 'Etkinlik');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product', 'Ürün');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate_button', 'Bağış düğmesi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_information', 'Benim bilgim');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_choose_which_information_you_would_like_to_download', 'Lütfen hangi bilgileri indirmek istediğinizi seçin.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_file', 'Dosya Oluştur');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_published_successfully', 'Etkinliğiniz başarıyla yayınlandı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_tickets_found', 'Bilet bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_tickets', 'Satın alınan biletler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_updated_successfully', 'Etkinliğiniz başarıyla güncellendi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_is_under_review', 'Ürününüz incelenmiştir');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_published_successfully', 'Ürününüz başarıyla yayınlandı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_product', 'Ürün Düzenle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_edited_successfully', 'Ürününüz başarıyla düzenlendi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'guest', 'Konuk');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket', 'Bilet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events_analytics', 'Olaylar Analytics');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id', 'İD');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_artists', 'Sanatçılar Tag');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_other_artists_to_show_they_performed_together', 'Birlikte yaptıklarını göstermek için diğer sanatçıları etiketleyin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_ticket', 'Bilet İndir');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_order_has_been_placed_successfully', 'Siparişiniz başarıyla verildi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order', 'Emir');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sale_invoice', 'Satış faturası');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_name', 'Satıcı Adı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_email', 'Satıcı e-postası');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invoice_to', 'Fatura');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_details', 'Ödeme detayları');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_due', 'Tam olarak');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank_name', 'banka adı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'item', 'Kalem');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_invoice', 'Faturayı indirin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'details', 'Detaylar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_products_found', 'Ürün bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_reviews_found', 'Yorum bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_about_to_purchase_the_items__do_you_want_to_proceed_', 'Öğeleri satın almak üzeresiniz, devam etmek ister misiniz?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request_a_refund', 'Geri ödeme istemek');
            $lang_update_queries[] = PT_UpdateLangs($value, 'new_orders_has_been_placed', 'Yeni siparişler verildi.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_status_has_been_changed', 'Sipariş durumunuz güncellendi.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_declined', 'Geri ödeme isteğiniz reddedildi.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_approved_your_money_added_to_your_wallet', 'Geri ödeme isteğiniz onaylandı, bakiye cüzdanınıza yeniden eklendi.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'added_tracking_info', 'İzleme bilgileri ile sipariş güncellendi.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_approved', 'Ürününüz onaylandı.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_your_event', 'Etkinliğinize katıldı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bought_a_ticket', 'Bir bilet aldım, yeni bir satışın var!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'orders', 'Emirler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_purchased', 'Bu öğeyi satın almadınız.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_not_found', 'Sipariş bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_status_wasn_t_set_to_delivered_within_60_days_from_the_order_date__it_will_be_automatically_be_sent_to__delivered_.', 'Sipariş durumu, sipariş tarihinden itibaren 60 gün içinde teslim edilmemişse, otomatik olarak teslim edilecek şekilde ayarlanacaktır.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_wasn_t_actually_delivered__the_buyer_can_request_a_refund.', 'Sipariş aslında teslim edilmediyse, alıcı bir geri ödeme talebinde bulunabilir.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_request_is_under_review', 'İsteğiniz inceleme altında.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request', 'Rica etmek');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_explain_the_reason', 'Lütfen nedeni açıklayın');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_products', 'Üst Ürünler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___products_this_week', 'Bu hafta en çok satan şarkılar ve ürünler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___albums_this_week', 'Bu hafta en çok satan şarkılar ve albümler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'accepted_', 'Kabul edilmiş');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_wait__this_may_take_few_minutes.', 'Lütfen bekleyin, bu birkaç dakika sürebilir.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'instead_of_uploading_a_song__you_can_easily_import_songs_using', 'Bir şarkı yüklemek yerine, kullanarak kolayca şarkı içe aktarabilirsiniz.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'imported_a_new_song_', 'Yeni bir şarkı ithal etti,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_has_been_sent_successfully', 'İnceleme başarıyla gönderildi!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product_', 'yeni ürün yaratıldı,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_event_', 'Yeni olay oluşturuldu,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_new_event_', 'Yeni etkinliğe katıldı,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_a_ticket_', 'bir bilet satın aldı,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_store', 'Benim hikayem');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_analytics', 'Mağaza Analytics');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products', 'Toplam Ürünler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_earned', 'Toplam Kazanç');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_commission', 'Toplam Komisyon');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_sub_earned', 'Toplam Alt Kazanılan');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_products', 'En çok satılan ürünler');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name_can_not_be_empty', 'Etkinlik adı boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description_can_not_be_empty', 'Etkinlik Açıklaması boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date_can_not_be_empty', 'Başlangıç ​​Tarihi boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_story', 'Hikaye yarat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_related_song_can_not_be_empty', 'Ürünle ilgili şarkı boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_info', 'Ürün bilgisi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_info', 'olay bilgisi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_the_owner', 'Sen sahibi değilsin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_not_found', 'Etkinlik bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'this_event_is_free', 'Bu etkinlik ücretsizdir');
            $lang_update_queries[] = PT_UpdateLangs($value, 'there_is_no_available_tickets', 'Müsait bilet yok');
            $lang_update_queries[] = PT_UpdateLangs($value, 'card_is_empty', 'Kart boş');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_can_not_be_empty', 'Adres boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id_can_not_be_empty', 'Kimlik boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location_can_not_be_empty', 'Olay yeri boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_time_can_not_be_empty', 'Başlangıç ​​zamanı boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date_can_not_be_empty', 'Bitiş tarihi boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_time_can_not_be_empty', 'Son zaman boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone_can_not_be_empty', 'Timezone boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_image_can_not_be_empty', 'Etkinlik görüntüsü boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_title_can_not_be_empty', 'Ürün başlığı boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_description_can_not_be_empty', 'Ürün Açıklaması boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_tags_can_not_be_empty', 'Ürün Etiketleri boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_price_can_not_be_empty', 'Ürün fiyatı boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_units_can_not_be_empty', 'Ürün üniteleri boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_category_can_not_be_empty', 'Ürün kategorisi boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_image_can_not_be_empty', 'Ürün resmi boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_not_found', 'ürün bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_not_found', 'Adres bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_url_can_not_be_empty', 'İzleme URL\'si boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number_can_not_be_empty', 'İzleme numarası boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_enter_a_valid_url', 'Lütfen geçerli bir adres girin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'rating_can_not_be_empty', 'derecelendirme boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_can_not_be_empty', 'İnceleme boş olamaz');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_who_can_see_the_story', 'Lütfen hikayeyi kim görebilir');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_image', 'Lütfen bir hikaye görüntüsü seçin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_song', 'Lütfen bir hikaye şarkısı seçin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_not_found_or_its_not_active', 'Hikaye bulunamadı ya da aktif değil');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified', 'Onaylanmış olmak');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_your_songs', 'şarkılarını sat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_products', 'Ürünleri sat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_events_and_sell_tickets', 'Etkinlikler oluşturun ve bilet satmak');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_more_songs', 'Daha fazla şarkı yükle');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_more_space', 'daha fazla yer al');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Özel görünümlü bir profil alın ve platformumuzda ünlü olun!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_was_purchased_in_sitename__date', 'Bilet {SITENAME}, {DATE} \'de satın alındı.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product', 'Yeni ürün oluşturdu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'track', 'İzlemek');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ticket', 'Olay bileti');
            $lang_update_queries[] = PT_UpdateLangs($value, 'for', 'İçin');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_starts', 'Etkinlik başlar');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ends', 'Olay biter');
            $lang_update_queries[] = PT_UpdateLangs($value, 'video_duration_must_be_less_than_or_equal_10_seconds', 'Video süresi 10 saniyeden az veya eşit olmalıdır');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_by', 'Tarafından satın alındı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_address', 'Olay adresi');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_orders_found', 'Daha fazla sipariş bulunamadı');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_to_purchase', 'Satın almak için giriş yapın');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_video_will_be_converted_to_mp3_soon__you_ll_get_notified_once_imported', 'Videonuz yakında MP3\'e dönüştürülecek, bir kez ithal edilecek');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_song_is_ready_to_view', 'Şarkınız görüntülenmeye hazır.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'Reviews', 'Yorumlar');
        } else if ($value == 'english') {
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_added_successfully_', 'Your address has been added successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_edited_successfully_', 'Your address has been edited successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_name_must_be_between_5_32_', 'Name must be between 5/32');
            $lang_update_queries[] = PT_UpdateLangs($value, '_the_url_is_invalid._please_enter_a_valid_url_', 'The URL is invalid, Please enter a valid URL.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image___video_', 'Media file is invalid, Please select a valid image / video.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image_', 'Media file is invalid, Please select a valid image.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_audio_', 'Media file is invalid, Please select a valid audio file.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_too_many_login_attempts_please_try_again_later_', 'Too many login attempts, please try again later.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_url_can_not_be_empty_', 'URL can not be empty.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_address_can_not_be_empty_', 'Address can not be empty.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tickets_available_and_ticket_price_can_not_be_empty_', 'Tickets availability and Price can\'t be empty.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_cover_can_not_be_empty_', 'Event Cover is required.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_video_can_not_be_empty_', 'Event Video is required.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_published_successfully_', 'Your event has been published successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_updated_successfully_', 'Your event has been updated successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_payment_successfully_done_', 'Payment successfully, Thank you!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_song_', 'Please select a song.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_valid_image_', 'Please select a valid image.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_published_successfully_', 'Your product has been published successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_is_under_review_', 'Your product is submitted and will be reviewed soon.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_edited_successfully_', 'Your product has been edited successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_some_products_don_t_have_enough_of_units_', 'Some of your products don\'t have enough units.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_wallet_', 'You don\'t have enough balance in your wallet.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_top_up_your_wallet_', 'Please top up your wallet');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_order_has_been_placed_successfully_', 'Your order has been placed!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tracking_info_has_been_saved_successfully_', 'Tracking info has been saved.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_review_has_been_sent_successfully_', 'Review has been sent.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_request_is_under_review_', 'Your request is under review.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_published_successfully_', 'Your story has been published successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_uploaded_successfully_to_publish_it_please_pay_', 'Your story has been uploaded, please pay to continue.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_story_not_found_or_its_active_', 'Story not found or not active.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_money_please_top_up_your_wallet_', 'You don\'t have enough balance in your wallet, please top up your wallet.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_linkedin', 'Login with LinkedIn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_vkontakte', 'Login with Vkontakte');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_instagram', 'Login with Instagram');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_qq', 'Login with QQ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_wechat', 'Login with WeChat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_discord', 'Login with Discord');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_mailru', 'Login with Mailru');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_items_found', 'No items found.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_wallet', 'You don\'t have enough balance in your wallet.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_top_up_your_wallet', 'Please top up your wallet.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total', 'Total');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new_address', 'Add New Address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_new_event', 'Create New Event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_events', 'Manage Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'browse_events', 'Browse Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_events', 'Joined Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_purchased_tickets', 'View Purchased Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name', 'Event Name');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location', 'Event location');
            $lang_update_queries[] = PT_UpdateLangs($value, 'online', 'Online');
            $lang_update_queries[] = PT_UpdateLangs($value, 'real_location', 'Real Location');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_date', 'Event Start Date');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_time', 'Event Start Time');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_date', 'Event End Date');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_time', 'Event End Time');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone', 'Timezone');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_tickets', 'Sell Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tickets_available_total_tickets_available_for_this_event_', 'Tickets available (Total tickets available for this event)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_price', 'Ticket Price');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description', 'Event Description');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_cover', 'Event Cover');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_video_trailer', 'Event Video/Trailer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_product', 'Create Product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_products', 'Manage Products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_item_units', 'Total Item Units');
            $lang_update_queries[] = PT_UpdateLangs($value, 'related_to_song', 'Related to song');
            $lang_update_queries[] = PT_UpdateLangs($value, 'images', 'Images');
            $lang_update_queries[] = PT_UpdateLangs($value, 'who_can_see', 'Who can see');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_my_followers_only', 'Show To My Followers');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_all_users', 'Show To All Users (Promotion)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_image', 'Story Image');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_song', 'Upload Song');
            $lang_update_queries[] = PT_UpdateLangs($value, 'shipped', 'Shipped');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivered', 'Delivered');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payments', 'Payments');
            $lang_update_queries[] = PT_UpdateLangs($value, 'subtotal', 'Subtotal');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund_money', 'Refund Money');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_details', 'Tracking Details');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_url', 'Site Url');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number', 'Tracking Number');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivery_address', 'Delivery Address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_orders_found', 'No orders found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'products', 'Products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_details', 'View Details');
            $lang_update_queries[] = PT_UpdateLangs($value, 'stories', 'Stories');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined', 'Joined');
            $lang_update_queries[] = PT_UpdateLangs($value, 'join', 'Join');
            $lang_update_queries[] = PT_UpdateLangs($value, 'buy_a_ticket', 'Buy a ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_trailer', 'View Trailer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_event', 'Edit Event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date', 'Start date');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date', 'End date');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_tickets', 'Available Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_people', 'Joined people');
            $lang_update_queries[] = PT_UpdateLangs($value, 'location', 'Location');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_events', 'Total Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_joined_users', 'Total Joined Users');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_available_tickets', 'Total Available Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_joined_events', 'Most joined events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_events', 'Most sold events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_events_found', 'No more events found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_tickets_found', 'No more tickets found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_products_found', 'No more products found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_reviews_found', 'No more reviews found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_successfully_done', 'payment successfully done');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_song_', 'Are you sure you want to pay to buy this song?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_album_', 'Are you sure you want to pay to buy this album?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_upgrade_to_pro_', 'Are you sure you want to upgrade to PRO?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_money_please_top_up_your_wallet', 'You dont have enough money please top up your wallet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'interested', 'Interested');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_views', 'No More Views');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_your_story_', 'Are you sure you want to delete your story?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_add_a_new_address', 'Please add a new address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_address', 'Please select address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund', 'Refund');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_event', 'Create Event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'checkout', 'Checkout');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_orders', 'Store Orders');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_orders', 'My Orders');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_request_found', 'No request found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_event', 'Delete Event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'cashfree', 'Cashfree');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paystack', 'Paystack');
            $lang_update_queries[] = PT_UpdateLangs($value, 'razorpay', 'Razorpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paysera', 'Paysera');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iyzipay', 'Iyzipay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payu', 'Payu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'securionpay', 'Securionpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'authorize', 'Authorize');
            $lang_update_queries[] = PT_UpdateLangs($value, 'placed', 'Placed');
            $lang_update_queries[] = PT_UpdateLangs($value, 'canceled', 'Canceled');
            $lang_update_queries[] = PT_UpdateLangs($value, 'packed', 'Packed');
            $lang_update_queries[] = PT_UpdateLangs($value, 'commission', 'Commission');
            $lang_update_queries[] = PT_UpdateLangs($value, 'final_price', 'Final Price');
            $lang_update_queries[] = PT_UpdateLangs($value, 'link', 'Link');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_commission', 'Site Commission');
            $lang_update_queries[] = PT_UpdateLangs($value, 'currently_unavailable.', 'Currently unavailable.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'write_review', 'Write Review');
            $lang_update_queries[] = PT_UpdateLangs($value, 'photos', 'Photos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_purchase', 'Verified Purchase');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events', 'Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_addresses', 'My Addresses');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new', 'Add New');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_address', 'Edit Address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'postcode___zip', 'Postcode / Zip');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invitation_links', 'Invitation Links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_links', 'Available Links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generated_links', 'Generated Links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'used_links', 'Used Links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_link', 'Generate Link');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invited_user', 'Invited User');
            $lang_update_queries[] = PT_UpdateLangs($value, 'date', 'Date');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copy', 'Copy');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copied', 'Copied');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_wallet', 'Available wallet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_up_wallet', 'Top up wallet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'hall_of_fame', 'Hall of fame');
            $lang_update_queries[] = PT_UpdateLangs($value, 'analytics', 'Analytics');
            $lang_update_queries[] = PT_UpdateLangs($value, 'more_info', 'More Info');
            $lang_update_queries[] = PT_UpdateLangs($value, 'listen_in_youtube', 'Listen in Youtube');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tagged_artists', 'Tagged Artists');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate', 'Donate');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_other', 'Other');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_clothes', 'Clothes');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_electronic', 'Electronic');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remove_from_cart', 'Remove From Cart');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_to_cart', 'Add To Cart');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_cart_is_empty.', 'Your cart is empty.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_address', 'Delete your address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_address_', 'Are you sure you want to delete this address?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_alert', 'Payment Alert');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_', 'Are you sure you want to pay?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_product', 'Delete your product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_product_', 'Are you sure you want to delete this product?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_for_story', 'Pay for story');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_for_create_story_', 'Are you sure you want to pay for create story?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_from_wallet', 'Pay from wallet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_buy_a_ticket_', 'Are you sure you want to buy a ticket?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave_event', 'Leave event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_leave_this_event_', 'Are you sure you want to leave this event?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave', 'Leave');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_event', 'Delete your event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_event_', 'Are you sure you want to delete this event?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get verified, sell your songs, Sell products, Create events and sell tickets, get a special looking profile and get famous on our platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get verified, sell your songs, Create events and sell tickets, get a special looking profile and get famous on our platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get verified, sell your songs, sell products, get a special looking profile and get famous on our platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get verified, sell your songs, get a special looking profile and get famous on our platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_events_found', 'No events found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event', 'Event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product', 'Product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate_button', 'Donate Button');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_information', 'My Information');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_choose_which_information_you_would_like_to_download', 'Please choose which information you would like to download.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_file', 'Generate file');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_published_successfully', 'Your event has been published successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_tickets_found', 'No tickets found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_tickets', 'Purchased Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_updated_successfully', 'Your event has been updated successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_is_under_review', 'Your product is under review');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_published_successfully', 'Your product has been published successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_product', 'Edit Product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_edited_successfully', 'Your product has been edited successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'guest', 'Guest');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket', 'Ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events_analytics', 'Events Analytics');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id', 'ID');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_artists', 'Tag Artists');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_other_artists_to_show_they_performed_together', 'Tag other artists to show they performed together');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_ticket', 'Download Ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_order_has_been_placed_successfully', 'Your order has been placed successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order', 'Order');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sale_invoice', 'Sale invoice');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_name', 'Seller Name');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_email', 'Seller Email');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invoice_to', 'Invoice To');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_details', 'Payment Details');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_due', 'Total Due');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank_name', 'Bank name');
            $lang_update_queries[] = PT_UpdateLangs($value, 'item', 'Item');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_invoice', 'Download Invoice');
            $lang_update_queries[] = PT_UpdateLangs($value, 'details', 'Details');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_products_found', 'No products found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_reviews_found', 'No reviews found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_about_to_purchase_the_items__do_you_want_to_proceed_', 'You are about to purchase the items, do you want to proceed?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request_a_refund', 'Request a Refund');
            $lang_update_queries[] = PT_UpdateLangs($value, 'new_orders_has_been_placed', 'New orders has been placed.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_status_has_been_changed', 'Your order status has been updated.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_declined', 'Your refund request has been declined.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_approved_your_money_added_to_your_wallet', 'Your refund request has been approved, balance re-added to your wallet.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'added_tracking_info', 'updated the order with tracking information.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_approved', 'Your product has been approved.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_your_event', 'joined your event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bought_a_ticket', 'bought a ticket, you got a new sale!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'orders', 'Orders');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_purchased', 'You didn\'t purchase this item.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_not_found', 'Order not found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_status_wasn_t_set_to_delivered_within_60_days_from_the_order_date__it_will_be_automatically_be_sent_to__delivered_.', 'If the order status wasn\'t set to delivered within 60 days from the order date, it will be automatically be set to Delivered.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_wasn_t_actually_delivered__the_buyer_can_request_a_refund.', 'If the order wasnt actually delivered, the buyer can request a refund.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_request_is_under_review', 'Your request is under review.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request', 'Request');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_explain_the_reason', 'Please explain the reason');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_products', 'Top Products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___products_this_week', 'Best Selling Songs &amp; Products This Week');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___albums_this_week', 'Best Selling Songs &amp; Albums This Week');
            $lang_update_queries[] = PT_UpdateLangs($value, 'accepted_', 'Accepted');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_wait__this_may_take_few_minutes.', 'Please wait, this may take few minutes.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'instead_of_uploading_a_song__you_can_easily_import_songs_using', 'Instead of uploading a song, you can easily import songs using');
            $lang_update_queries[] = PT_UpdateLangs($value, 'imported_a_new_song_', 'Imported a new song,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_has_been_sent_successfully', 'Review has been sent successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product_', 'created new product,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_event_', 'created new event,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_new_event_', 'joined new event,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_a_ticket_', 'purchased a ticket,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_store', 'My Store');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_analytics', 'Store Analytics');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products', 'Total Products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_earned', 'Total Earned');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_commission', 'Total Commission');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_sub_earned', 'Total Sub Earned');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_products', 'Most sold products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name_can_not_be_empty', 'Event name can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description_can_not_be_empty', 'Event description can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date_can_not_be_empty', 'Start date can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_story', 'Create Story');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_related_song_can_not_be_empty', 'Product related song can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_info', 'Product Info');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_info', 'event Info');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_the_owner', 'You are not the owner');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_not_found', 'Event not found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'this_event_is_free', 'This event is free');
            $lang_update_queries[] = PT_UpdateLangs($value, 'there_is_no_available_tickets', 'There is no available tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'card_is_empty', 'Card is empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_can_not_be_empty', 'Address can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id_can_not_be_empty', 'id can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location_can_not_be_empty', 'Event location can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_time_can_not_be_empty', 'Start time can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date_can_not_be_empty', 'End Date can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_time_can_not_be_empty', 'End time can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone_can_not_be_empty', 'Timezone can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_image_can_not_be_empty', 'Event image can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_title_can_not_be_empty', 'Product title can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_description_can_not_be_empty', 'Product description can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_tags_can_not_be_empty', 'Product tags can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_price_can_not_be_empty', 'Product price can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_units_can_not_be_empty', 'Product units can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_category_can_not_be_empty', 'Product category can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_image_can_not_be_empty', 'Product image can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_not_found', 'Product not found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_not_found', 'Address not found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_url_can_not_be_empty', 'Tracking url can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number_can_not_be_empty', 'Tracking number can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_enter_a_valid_url', 'Please enter a valid url');
            $lang_update_queries[] = PT_UpdateLangs($value, 'rating_can_not_be_empty', 'rating can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_can_not_be_empty', 'review can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_who_can_see_the_story', 'Please who can see the story');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_image', 'Please select a story image');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_song', 'Please select a story song');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_not_found_or_its_not_active', 'Story not found or its not active');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified', 'Get verified');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_your_songs', 'sell your songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_products', 'sell products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_events_and_sell_tickets', 'Create events and sell tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_more_songs', 'upload more songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_more_space', 'get more space');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_a_special_looking_profile_and_get_famous_on_our_platform_', 'get a special looking profile and get famous on our platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_was_purchased_in_sitename__date', 'Ticket was purchased in {SITENAME}, {DATE}');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product', 'Created new product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'track', 'Track');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ticket', 'Event Ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'for', 'For');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_starts', 'Event Starts');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ends', 'Event Ends');
            $lang_update_queries[] = PT_UpdateLangs($value, 'video_duration_must_be_less_than_or_equal_10_seconds', 'Video duration must be less than or equal 10 seconds');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_by', 'Purchased By');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_address', 'Event Address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_orders_found', 'No more orders found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_to_purchase', 'Login To Purchase');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_video_will_be_converted_to_mp3_soon__you_ll_get_notified_once_imported', 'Your video will be converted to mp3 soon, youll get notified once imported');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_song_is_ready_to_view', 'Your song is ready to view.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'Reviews', 'Reviews');
        } else if ($value != 'english') {
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_added_successfully_', 'Your address has been added successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_address_has_been_edited_successfully_', 'Your address has been edited successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_name_must_be_between_5_32_', 'Name must be between 5/32');
            $lang_update_queries[] = PT_UpdateLangs($value, '_the_url_is_invalid._please_enter_a_valid_url_', 'The URL is invalid, Please enter a valid URL.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image___video_', 'Media file is invalid, Please select a valid image / video.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_image_', 'Media file is invalid, Please select a valid image.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_media_file_is_invalid._please_select_a_valid_audio_', 'Media file is invalid, Please select a valid audio file.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_too_many_login_attempts_please_try_again_later_', 'Too many login attempts, please try again later.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_url_can_not_be_empty_', 'URL can not be empty.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_address_can_not_be_empty_', 'Address can not be empty.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tickets_available_and_ticket_price_can_not_be_empty_', 'Tickets availability and Price can\'t be empty.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_cover_can_not_be_empty_', 'Event Cover is required.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_event_video_can_not_be_empty_', 'Event Video is required.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_published_successfully_', 'Your event has been published successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_event_has_been_updated_successfully_', 'Your event has been updated successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_payment_successfully_done_', 'Payment successfully, Thank you!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_song_', 'Please select a song.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_select_a_valid_image_', 'Please select a valid image.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_published_successfully_', 'Your product has been published successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_is_under_review_', 'Your product is submitted and will be reviewed soon.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_product_has_been_edited_successfully_', 'Your product has been edited successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_some_products_don_t_have_enough_of_units_', 'Some of your products don\'t have enough units.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_wallet_', 'You don\'t have enough balance in your wallet.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_please_top_up_your_wallet_', 'Please top up your wallet');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_order_has_been_placed_successfully_', 'Your order has been placed!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_tracking_info_has_been_saved_successfully_', 'Tracking info has been saved.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_review_has_been_sent_successfully_', 'Review has been sent.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_request_is_under_review_', 'Your request is under review.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_published_successfully_', 'Your story has been published successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, '_your_story_has_been_uploaded_successfully_to_publish_it_please_pay_', 'Your story has been uploaded, please pay to continue.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_story_not_found_or_its_active_', 'Story not found or not active.');
            $lang_update_queries[] = PT_UpdateLangs($value, '_you_don_t_have_enough_money_please_top_up_your_wallet_', 'You don\'t have enough balance in your wallet, please top up your wallet.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_linkedin', 'Login with LinkedIn');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_vkontakte', 'Login with Vkontakte');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_instagram', 'Login with Instagram');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_qq', 'Login with QQ');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_wechat', 'Login with WeChat');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_discord', 'Login with Discord');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_with_mailru', 'Login with Mailru');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_items_found', 'No items found.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_wallet', 'You don\'t have enough balance in your wallet.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_top_up_your_wallet', 'Please top up your wallet.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total', 'Total');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new_address', 'Add New Address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_new_event', 'Create New Event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_events', 'Manage Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'browse_events', 'Browse Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_events', 'Joined Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_purchased_tickets', 'View Purchased Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name', 'Event Name');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location', 'Event location');
            $lang_update_queries[] = PT_UpdateLangs($value, 'online', 'Online');
            $lang_update_queries[] = PT_UpdateLangs($value, 'real_location', 'Real Location');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_date', 'Event Start Date');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_start_time', 'Event Start Time');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_date', 'Event End Date');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_end_time', 'Event End Time');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone', 'Timezone');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_tickets', 'Sell Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tickets_available_total_tickets_available_for_this_event_', 'Tickets available (Total tickets available for this event)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_price', 'Ticket Price');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description', 'Event Description');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_cover', 'Event Cover');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_video_trailer', 'Event Video/Trailer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_product', 'Create Product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'manage_products', 'Manage Products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_item_units', 'Total Item Units');
            $lang_update_queries[] = PT_UpdateLangs($value, 'related_to_song', 'Related to song');
            $lang_update_queries[] = PT_UpdateLangs($value, 'images', 'Images');
            $lang_update_queries[] = PT_UpdateLangs($value, 'who_can_see', 'Who can see');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_my_followers_only', 'Show To My Followers');
            $lang_update_queries[] = PT_UpdateLangs($value, 'show_to_all_users', 'Show To All Users (Promotion)');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_image', 'Story Image');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_song', 'Upload Song');
            $lang_update_queries[] = PT_UpdateLangs($value, 'shipped', 'Shipped');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivered', 'Delivered');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payments', 'Payments');
            $lang_update_queries[] = PT_UpdateLangs($value, 'subtotal', 'Subtotal');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund_money', 'Refund Money');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_details', 'Tracking Details');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_url', 'Site Url');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number', 'Tracking Number');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delivery_address', 'Delivery Address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_orders_found', 'No orders found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'products', 'Products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_details', 'View Details');
            $lang_update_queries[] = PT_UpdateLangs($value, 'stories', 'Stories');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined', 'Joined');
            $lang_update_queries[] = PT_UpdateLangs($value, 'join', 'Join');
            $lang_update_queries[] = PT_UpdateLangs($value, 'buy_a_ticket', 'Buy a ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'view_trailer', 'View Trailer');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_event', 'Edit Event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date', 'Start date');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date', 'End date');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_tickets', 'Available Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_people', 'Joined people');
            $lang_update_queries[] = PT_UpdateLangs($value, 'location', 'Location');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_events', 'Total Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_joined_users', 'Total Joined Users');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_available_tickets', 'Total Available Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_joined_events', 'Most joined events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_events', 'Most sold events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_events_found', 'No more events found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_tickets_found', 'No more tickets found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_products_found', 'No more products found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_reviews_found', 'No more reviews found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_successfully_done', 'payment successfully done');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_song_', 'Are you sure you want to pay to buy this song?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_buy_album_', 'Are you sure you want to pay to buy this album?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_to_upgrade_to_pro_', 'Are you sure you want to upgrade to PRO?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_don_t_have_enough_money_please_top_up_your_wallet', 'You dont have enough money please top up your wallet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'interested', 'Interested');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_views', 'No More Views');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_your_story_', 'Are you sure you want to delete your story?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_add_a_new_address', 'Please add a new address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_address', 'Please select address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'refund', 'Refund');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_event', 'Create Event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'checkout', 'Checkout');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_orders', 'Store Orders');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_orders', 'My Orders');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_request_found', 'No request found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_event', 'Delete Event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'cashfree', 'Cashfree');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paystack', 'Paystack');
            $lang_update_queries[] = PT_UpdateLangs($value, 'razorpay', 'Razorpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'paysera', 'Paysera');
            $lang_update_queries[] = PT_UpdateLangs($value, 'iyzipay', 'Iyzipay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payu', 'Payu');
            $lang_update_queries[] = PT_UpdateLangs($value, 'securionpay', 'Securionpay');
            $lang_update_queries[] = PT_UpdateLangs($value, 'authorize', 'Authorize');
            $lang_update_queries[] = PT_UpdateLangs($value, 'placed', 'Placed');
            $lang_update_queries[] = PT_UpdateLangs($value, 'canceled', 'Canceled');
            $lang_update_queries[] = PT_UpdateLangs($value, 'packed', 'Packed');
            $lang_update_queries[] = PT_UpdateLangs($value, 'commission', 'Commission');
            $lang_update_queries[] = PT_UpdateLangs($value, 'final_price', 'Final Price');
            $lang_update_queries[] = PT_UpdateLangs($value, 'link', 'Link');
            $lang_update_queries[] = PT_UpdateLangs($value, 'site_commission', 'Site Commission');
            $lang_update_queries[] = PT_UpdateLangs($value, 'currently_unavailable.', 'Currently unavailable.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'write_review', 'Write Review');
            $lang_update_queries[] = PT_UpdateLangs($value, 'photos', 'Photos');
            $lang_update_queries[] = PT_UpdateLangs($value, 'verified_purchase', 'Verified Purchase');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events', 'Events');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_addresses', 'My Addresses');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_new', 'Add New');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_address', 'Edit Address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'postcode___zip', 'Postcode / Zip');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invitation_links', 'Invitation Links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_links', 'Available Links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generated_links', 'Generated Links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'used_links', 'Used Links');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_link', 'Generate Link');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invited_user', 'Invited User');
            $lang_update_queries[] = PT_UpdateLangs($value, 'date', 'Date');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copy', 'Copy');
            $lang_update_queries[] = PT_UpdateLangs($value, 'copied', 'Copied');
            $lang_update_queries[] = PT_UpdateLangs($value, 'available_wallet', 'Available wallet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_up_wallet', 'Top up wallet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'hall_of_fame', 'Hall of fame');
            $lang_update_queries[] = PT_UpdateLangs($value, 'analytics', 'Analytics');
            $lang_update_queries[] = PT_UpdateLangs($value, 'more_info', 'More Info');
            $lang_update_queries[] = PT_UpdateLangs($value, 'listen_in_youtube', 'Listen in Youtube');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tagged_artists', 'Tagged Artists');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate', 'Donate');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_other', 'Other');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_clothes', 'Clothes');
            $lang_update_queries[] = PT_UpdateLangs($value, 's_electronic', 'Electronic');
            $lang_update_queries[] = PT_UpdateLangs($value, 'remove_from_cart', 'Remove From Cart');
            $lang_update_queries[] = PT_UpdateLangs($value, 'add_to_cart', 'Add To Cart');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_cart_is_empty.', 'Your cart is empty.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_address', 'Delete your address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_address_', 'Are you sure you want to delete this address?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_alert', 'Payment Alert');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_', 'Are you sure you want to pay?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_product', 'Delete your product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_product_', 'Are you sure you want to delete this product?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_for_story', 'Pay for story');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_pay_for_create_story_', 'Are you sure you want to pay for create story?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'pay_from_wallet', 'Pay from wallet');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_buy_a_ticket_', 'Are you sure you want to buy a ticket?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave_event', 'Leave event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_leave_this_event_', 'Are you sure you want to leave this event?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'leave', 'Leave');
            $lang_update_queries[] = PT_UpdateLangs($value, 'delete_your_event', 'Delete your event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'are_you_sure_you_want_to_delete_this_event_', 'Are you sure you want to delete this event?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get verified, sell your songs, Sell products, Create events and sell tickets, get a special looking profile and get famous on our platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get verified, sell your songs, Create events and sell tickets, get a special looking profile and get famous on our platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___sell_products___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get verified, sell your songs, sell products, get a special looking profile and get famous on our platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified___sell_your_songs___get_a_special_looking_profile_and_get_famous_on_our_platform_', 'Get verified, sell your songs, get a special looking profile and get famous on our platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_events_found', 'No events found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event', 'Event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product', 'Product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'donate_button', 'Donate Button');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_information', 'My Information');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_choose_which_information_you_would_like_to_download', 'Please choose which information you would like to download.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'generate_file', 'Generate file');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_published_successfully', 'Your event has been published successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_tickets_found', 'No tickets found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_tickets', 'Purchased Tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_event_has_been_updated_successfully', 'Your event has been updated successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_is_under_review', 'Your product is under review');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_published_successfully', 'Your product has been published successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'edit_product', 'Edit Product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_edited_successfully', 'Your product has been edited successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'guest', 'Guest');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket', 'Ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'events_analytics', 'Events Analytics');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id', 'ID');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_artists', 'Tag Artists');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tag_other_artists_to_show_they_performed_together', 'Tag other artists to show they performed together');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_ticket', 'Download Ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_order_has_been_placed_successfully', 'Your order has been placed successfully');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order', 'Order');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sale_invoice', 'Sale invoice');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_name', 'Seller Name');
            $lang_update_queries[] = PT_UpdateLangs($value, 'seller_email', 'Seller Email');
            $lang_update_queries[] = PT_UpdateLangs($value, 'invoice_to', 'Invoice To');
            $lang_update_queries[] = PT_UpdateLangs($value, 'payment_details', 'Payment Details');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_due', 'Total Due');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bank_name', 'Bank name');
            $lang_update_queries[] = PT_UpdateLangs($value, 'item', 'Item');
            $lang_update_queries[] = PT_UpdateLangs($value, 'download_invoice', 'Download Invoice');
            $lang_update_queries[] = PT_UpdateLangs($value, 'details', 'Details');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_products_found', 'No products found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_reviews_found', 'No reviews found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_about_to_purchase_the_items__do_you_want_to_proceed_', 'You are about to purchase the items, do you want to proceed?');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request_a_refund', 'Request a Refund');
            $lang_update_queries[] = PT_UpdateLangs($value, 'new_orders_has_been_placed', 'New orders has been placed.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_status_has_been_changed', 'Your order status has been updated.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_declined', 'Your refund request has been declined.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_refund_request_has_been_approved_your_money_added_to_your_wallet', 'Your refund request has been approved, balance re-added to your wallet.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'added_tracking_info', 'updated the order with tracking information.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_product_has_been_approved', 'Your product has been approved.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_your_event', 'joined your event');
            $lang_update_queries[] = PT_UpdateLangs($value, 'bought_a_ticket', 'bought a ticket, you got a new sale!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'orders', 'Orders');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_purchased', 'You didn\'t purchase this item.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'order_not_found', 'Order not found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_status_wasn_t_set_to_delivered_within_60_days_from_the_order_date__it_will_be_automatically_be_sent_to__delivered_.', 'If the order status wasn\'t set to delivered within 60 days from the order date, it will be automatically be set to Delivered.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'if_the_order_wasn_t_actually_delivered__the_buyer_can_request_a_refund.', 'If the order wasnt actually delivered, the buyer can request a refund.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_request_is_under_review', 'Your request is under review.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'request', 'Request');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_explain_the_reason', 'Please explain the reason');
            $lang_update_queries[] = PT_UpdateLangs($value, 'top_products', 'Top Products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___products_this_week', 'Best Selling Songs &amp; Products This Week');
            $lang_update_queries[] = PT_UpdateLangs($value, 'best_selling_songs___albums_this_week', 'Best Selling Songs &amp; Albums This Week');
            $lang_update_queries[] = PT_UpdateLangs($value, 'accepted_', 'Accepted');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_wait__this_may_take_few_minutes.', 'Please wait, this may take few minutes.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'instead_of_uploading_a_song__you_can_easily_import_songs_using', 'Instead of uploading a song, you can easily import songs using');
            $lang_update_queries[] = PT_UpdateLangs($value, 'imported_a_new_song_', 'Imported a new song,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_has_been_sent_successfully', 'Review has been sent successfully!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product_', 'created new product,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_event_', 'created new event,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'joined_new_event_', 'joined new event,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_a_ticket_', 'purchased a ticket,');
            $lang_update_queries[] = PT_UpdateLangs($value, 'my_store', 'My Store');
            $lang_update_queries[] = PT_UpdateLangs($value, 'store_analytics', 'Store Analytics');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_products', 'Total Products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_earned', 'Total Earned');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_commission', 'Total Commission');
            $lang_update_queries[] = PT_UpdateLangs($value, 'total_sub_earned', 'Total Sub Earned');
            $lang_update_queries[] = PT_UpdateLangs($value, 'most_sold_products', 'Most sold products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_name_can_not_be_empty', 'Event name can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_description_can_not_be_empty', 'Event description can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_date_can_not_be_empty', 'Start date can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_story', 'Create Story');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_related_song_can_not_be_empty', 'Product related song can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_info', 'Product Info');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_info', 'event Info');
            $lang_update_queries[] = PT_UpdateLangs($value, 'you_are_not_the_owner', 'You are not the owner');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_not_found', 'Event not found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'this_event_is_free', 'This event is free');
            $lang_update_queries[] = PT_UpdateLangs($value, 'there_is_no_available_tickets', 'There is no available tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'card_is_empty', 'Card is empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_can_not_be_empty', 'Address can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'id_can_not_be_empty', 'id can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_location_can_not_be_empty', 'Event location can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'start_time_can_not_be_empty', 'Start time can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_date_can_not_be_empty', 'End Date can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'end_time_can_not_be_empty', 'End time can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'timezone_can_not_be_empty', 'Timezone can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_image_can_not_be_empty', 'Event image can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_title_can_not_be_empty', 'Product title can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_description_can_not_be_empty', 'Product description can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_tags_can_not_be_empty', 'Product tags can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_price_can_not_be_empty', 'Product price can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_units_can_not_be_empty', 'Product units can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_category_can_not_be_empty', 'Product category can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_image_can_not_be_empty', 'Product image can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'product_not_found', 'Product not found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'address_not_found', 'Address not found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_url_can_not_be_empty', 'Tracking url can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'tracking_number_can_not_be_empty', 'Tracking number can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_enter_a_valid_url', 'Please enter a valid url');
            $lang_update_queries[] = PT_UpdateLangs($value, 'rating_can_not_be_empty', 'rating can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'review_can_not_be_empty', 'review can not be empty');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_who_can_see_the_story', 'Please who can see the story');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_image', 'Please select a story image');
            $lang_update_queries[] = PT_UpdateLangs($value, 'please_select_a_story_song', 'Please select a story song');
            $lang_update_queries[] = PT_UpdateLangs($value, 'story_not_found_or_its_not_active', 'Story not found or its not active');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_verified', 'Get verified');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_your_songs', 'sell your songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'sell_products', 'sell products');
            $lang_update_queries[] = PT_UpdateLangs($value, 'create_events_and_sell_tickets', 'Create events and sell tickets');
            $lang_update_queries[] = PT_UpdateLangs($value, 'upload_more_songs', 'upload more songs');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_more_space', 'get more space');
            $lang_update_queries[] = PT_UpdateLangs($value, 'get_a_special_looking_profile_and_get_famous_on_our_platform_', 'get a special looking profile and get famous on our platform!');
            $lang_update_queries[] = PT_UpdateLangs($value, 'ticket_was_purchased_in_sitename__date', 'Ticket was purchased in {SITENAME}, {DATE}');
            $lang_update_queries[] = PT_UpdateLangs($value, 'created_new_product', 'Created new product');
            $lang_update_queries[] = PT_UpdateLangs($value, 'track', 'Track');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ticket', 'Event Ticket');
            $lang_update_queries[] = PT_UpdateLangs($value, 'for', 'For');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_starts', 'Event Starts');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_ends', 'Event Ends');
            $lang_update_queries[] = PT_UpdateLangs($value, 'video_duration_must_be_less_than_or_equal_10_seconds', 'Video duration must be less than or equal 10 seconds');
            $lang_update_queries[] = PT_UpdateLangs($value, 'purchased_by', 'Purchased By');
            $lang_update_queries[] = PT_UpdateLangs($value, 'event_address', 'Event Address');
            $lang_update_queries[] = PT_UpdateLangs($value, 'no_more_orders_found', 'No more orders found');
            $lang_update_queries[] = PT_UpdateLangs($value, 'login_to_purchase', 'Login To Purchase');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_video_will_be_converted_to_mp3_soon__you_ll_get_notified_once_imported', 'Your video will be converted to mp3 soon, youll get notified once imported');
            $lang_update_queries[] = PT_UpdateLangs($value, 'your_song_is_ready_to_view', 'Your song is ready to view.');
            $lang_update_queries[] = PT_UpdateLangs($value, 'Reviews', 'Reviews');
        }
    }
    if (!empty($lang_update_queries)) {
        foreach ($lang_update_queries as $key => $query) {
            $sql = mysqli_query($mysqli, $query);
        }
        $sql2 = mysqli_query($mysqli, "INSERT INTO `html_emails` (`id`, `name`, `value`) VALUES (NULL, 'unusual_login', 'Hi {{username}},<br><br>\r\n\r\nPlease verify that it\'s you<br><br>\r\n\r\nYour sign in attempt seems a little different than usual. This could be because you are signing in from a different device or a different location.<br><br>\r\n\r\nIf you are attempting to sign-in, please use the following code to confirm your identity:<br><br>\r\n\r\n{{code}}<br><br>\r\n\r\nHere are the details of the sign-in attempt:<br>\r\nDate: {{date}}<br>\r\nAccount: {{email}}<br>\r\nLocation: {{countryCode}}<br>\r\nIP Address: {{ip_address}}<br>\r\nCity: {{city}}<br><br>\r\n\r\nIf this wasn\'t you, please reset your password.<br><br>\r\n\r\nYours securely,<br>\r\nTeam {{site_name}}');");
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
                     <h2 class="light">Update to v1.4 </span></h2>
                     <div class="setting-well">
                        <h4>Changelog</h4>
                        <ul class="wo_update_changelog">
                            <li>[Added] razorpay, securionpay, payu, paystack, iyzipay, checkout, cashfree & authorize.net payment methods.</li>
                            <li>[Added] the ability to import songs from YouTube + Video Player.</li>
                            <li>[Added] events system, artists can create events and sell tickets + admin commission. </li>
                            <li>[Added] store system, artists can sell items related to their song + admin commission.</li>
                            <li>[Added] the ability to upload video trailer as channel cover or musician into.</li>
                            <li>[Added] story system, users can upload stories with audio only, free & paid stories. </li>
                            <li>[Added] the ability to edit SEO tags for each page.</li>
                            <li>[Added] digitalocean spaces support.</li>
                            <li>[Added] LinkedIn, Vkontakte, Instagram, QQ, WeChat, Discord & Mailru social login. </li>
                            <li>[Added] manage emails and user invitation system to admin panel. </li>
                            <li>[Added] 24 link validation for reset password. </li>
                            <li>[Added] the ability to edit SEO from admin panel. </li>
                            <li>[Added] more APIs</li>
                            <li>[Updated] admin panel.</li>
                            <li>[Fixed] 15+ minor reported bugs.</li>
                            <li>[Improved] speed.</li>
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
    
    "ALTER TABLE `users` ADD `time_code_sent` INT(11) NOT NULL DEFAULT '0' AFTER `email_on_comment_mention`, ADD INDEX (`time_code_sent`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'cashfree_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'cashfree_mode', 'sandbox');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'cashfree_client_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'cashfree_secret_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'paystack_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'paystack_secret_key', '');",
    "ALTER TABLE `users` ADD `paystack_ref` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `time`, ADD INDEX (`paystack_ref`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'razorpay_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'razorpay_key_id', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'razorpay_key_secret', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'paysera_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'paysera_mode', '1');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'paysera_project_id', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'paysera_sign_password', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'checkout_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'checkout_mode', 'sandbox');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'checkout_currency', 'USD');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'checkout_seller_id', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'checkout_publishable_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'checkout_private_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_mode', '1');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_secret_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_buyer_id', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_buyer_name', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_buyer_surname', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_buyer_gsm_number', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_buyer_email', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_identity_number', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_address', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_city', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_country', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'iyzipay_zip', '');",
    "ALTER TABLE `users` ADD `ConversationId` INT(20) NOT NULL DEFAULT '0' AFTER `paystack_ref`, ADD INDEX (`ConversationId`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'payu_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'payu_mode', '1');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'payu_merchant_id', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'payu_secret_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'payu_buyer_name', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'payu_buyer_surname', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'payu_buyer_gsm_number', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'payu_buyer_email', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'securionpay_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'securionpay_public_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'securionpay_secret_key', '');",
    "ALTER TABLE `users` ADD `securionpay_key` INT(30) NOT NULL DEFAULT '0' AFTER `ConversationId`, ADD INDEX (`securionpay_key`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'authorize_payment', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'authorize_test_mode', 'SANDBOX');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'authorize_login_id', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'authorize_transaction_key', '');",
    "CREATE TABLE `html_emails` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `value` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , PRIMARY KEY (`id`), INDEX (`name`)) ENGINE = InnoDB;",
    "INSERT INTO `html_emails` (`id`, `name`, `value`) VALUES (NULL, 'notification', 'Hello {{uname}},\r\n<br><br>\r\nNew notification from <a href=\"{{notify_link}}\">{{username}}{{c}}</a>:\r\n<br>\r\n{{full_name}} {{contents}}\r\n<a href=\"{{user_link}}\">{{username}}{{c}}</a>\r\n<br><br>\r\n{{site_name}} Team.');",
    "INSERT INTO `html_emails` (`id`, `name`, `value`) VALUES (NULL, 'confirm', 'Hello {{USERNAME}},\r\n<br><br>\r\nPlease confirm your email address by clicking the link below:\r\n<br>\r\n<a href=\"{{CODE_URL}}\">Confirm email address</a>\r\n<br><br>\r\n{{SITE_NAME}} Team.');",
    "INSERT INTO `html_emails` (`id`, `name`, `value`) VALUES (NULL, 'reset', 'Hello {{username}},\r\n<br><br>\r\nTo reset your password, please click the link below:\r\n<br>\r\n<a href=\"{{code_url}}\">Reset my password</a>\r\n<br><br>\r\n{{site_name}} Team.');",
    "CREATE TABLE `invitation_links` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `invited_id` INT(11) NOT NULL DEFAULT '0' , `code` VARCHAR(300) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `time` INT(50) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`invited_id`), INDEX (`code`), INDEX (`time`)) ENGINE = InnoDB;",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'invite_links_system', '0');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'user_links_limit', '10');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'expire_user_links', 'month');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'event_system', '1');",
    "CREATE TABLE `events` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `name` VARCHAR(150) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `start_date` DATE NOT NULL , `start_time` TIME NOT NULL , `end_date` DATE NOT NULL , `end_time` TIME NOT NULL , `timezone` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `online_url` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `real_address` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `available_tickets` INT(11) NOT NULL DEFAULT '0' , `ticket_price` FLOAT(11) NOT NULL DEFAULT '0' , `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `video` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`start_date`), INDEX (`start_time`), INDEX (`end_date`), INDEX (`end_time`), INDEX (`timezone`), INDEX (`available_tickets`), INDEX (`ticket_price`), INDEX (`time`)) ENGINE = InnoDB;",
    "ALTER TABLE `events` ADD `user_id` INT(11) NOT NULL DEFAULT '0' AFTER `id`, ADD INDEX (`user_id`);",
    "CREATE TABLE `events_joined` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `event_id` INT(11) NOT NULL DEFAULT '0' , `user_id` INT(11) NOT NULL DEFAULT '0' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`event_id`), INDEX (`user_id`), INDEX (`time`)) ENGINE = InnoDB;",
    "ALTER TABLE `events` ADD `240p` INT(11) NOT NULL DEFAULT '0' AFTER `video`, ADD `360p` INT(11) NOT NULL DEFAULT '0' AFTER `240p`, ADD `480p` INT(11) NOT NULL DEFAULT '0' AFTER `360p`, ADD `720p` INT(11) NOT NULL DEFAULT '0' AFTER `480p`, ADD `1080p` INT(11) NOT NULL DEFAULT '0' AFTER `720p`, ADD `2048p` INT(11) NOT NULL DEFAULT '0' AFTER `1080p`, ADD `4096p` INT(11) NOT NULL DEFAULT '0' AFTER `2048p`;",
    "ALTER TABLE `purchases` ADD `event_id` INT(11) NOT NULL DEFAULT '0' AFTER `user_id`, ADD INDEX (`event_id`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'channel_trailer', 'on');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'channel_trailer_upload', 'artist');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'story_system', 'on');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'story_price', '0');",
    "CREATE TABLE `story` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `audio` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `paid` INT(11) NOT NULL DEFAULT '0' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`paid`), INDEX (`time`)) ENGINE = InnoDB;",
    "ALTER TABLE `story` ADD `active` INT(11) NOT NULL DEFAULT '0' AFTER `paid`, ADD INDEX (`active`);",
    "ALTER TABLE `story` ADD `url` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `paid`;",
    "CREATE TABLE `story_seen` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `story_id` INT(11) NOT NULL DEFAULT '0' , `paid` INT(11) NOT NULL DEFAULT '0' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`story_id`), INDEX (`paid`), INDEX (`time`)) ENGINE = InnoDB;",
    "ALTER TABLE `story_seen` ADD `story_owner_id` INT(11) NOT NULL DEFAULT '0' AFTER `user_id`, ADD INDEX (`story_owner_id`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'store_system', 'on');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'store_review_system', 'off');",
    "CREATE TABLE `media` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `product_id` INT(11) NOT NULL DEFAULT '0' , `image` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`product_id`)) ENGINE = InnoDB;",
    "CREATE TABLE `products` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `title` VARCHAR(200) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `desc` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `tags` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `price` FLOAT(11) NOT NULL DEFAULT '0' , `related_song` INT(11) NOT NULL DEFAULT '0' , `cat_id` INT(11) NOT NULL DEFAULT '0' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`price`), INDEX (`related_song`)) ENGINE = InnoDB;",
    "ALTER TABLE `products` ADD `active` INT(11) NOT NULL DEFAULT '0' AFTER `cat_id`, ADD INDEX (`active`);",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'store_commission', '0');",
    "CREATE TABLE `products_category` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `lang_key` VARCHAR(50) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`lang_key`)) ENGINE = InnoDB;",
    "CREATE TABLE `orders` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `product_owner_id` INT(11) NOT NULL DEFAULT '0' , `product_id` INT(11) NOT NULL DEFAULT '0' , `price` FLOAT(11) NOT NULL DEFAULT '0' , `commission` FLOAT(11) NOT NULL DEFAULT '0' , `final_price` FLOAT(11) NOT NULL DEFAULT '0' , `status` INT(2) NOT NULL DEFAULT '0' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`product_owner_id`), INDEX (`product_id`), INDEX (`final_price`), INDEX (`status`), INDEX (`time`)) ENGINE = InnoDB;",
    "CREATE TABLE `address` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `name` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `phone` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `country` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `city` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `zip` VARCHAR(20) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `address` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`)) ENGINE = InnoDB;",
    "CREATE TABLE `card` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `product_id` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`product_id`)) ENGINE = InnoDB;",
    "ALTER TABLE `products` ADD `units` INT(11) NULL DEFAULT '0' AFTER `cat_id`, ADD INDEX (`units`);",
    "ALTER TABLE `card` ADD `units` INT(11) NOT NULL DEFAULT '0' AFTER `product_id`, ADD INDEX (`units`);",
    "ALTER TABLE `orders` ADD `hash_id` VARCHAR(120) NULL DEFAULT 0 AFTER `id`, ADD INDEX (`hash_id`);",
    "ALTER TABLE `orders` CHANGE `status` `status` VARCHAR(30) NOT NULL DEFAULT 'placed';",
    "ALTER TABLE `orders` ADD `units` INT(11) NOT NULL DEFAULT '0' AFTER `final_price`, ADD INDEX (`units`);",
    "ALTER TABLE `orders` ADD `tracking_url` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL AFTER `units`, ADD `tracking_id` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `tracking_url`;",
    "CREATE TABLE `review` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `product_id` INT(11) NOT NULL DEFAULT '0' , `review` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `star` INT(11) NOT NULL DEFAULT '1' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`product_id`), INDEX (`star`)) ENGINE = InnoDB;",
    "ALTER TABLE `media` ADD `review_id` INT(11) NOT NULL DEFAULT '0' AFTER `product_id`, ADD INDEX (`review_id`);",
    "ALTER TABLE `orders` ADD `address_id` INT(11) NOT NULL DEFAULT '0' AFTER `product_id`, ADD INDEX (`address_id`);",
    "CREATE TABLE `refund` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `order_hash_id` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' , `message` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`order_hash_id`)) ENGINE = InnoDB;",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'linkedin_login', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'vkontakte_login', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'instagram_login', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'qq_login', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'wechat_login', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'discord_login', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'mailru_login', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'linkedinAppId', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'linkedinAppKey', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'VkontakteAppId', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'VkontakteAppKey', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'instagramAppId', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'instagramAppkey', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'qqAppId', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'qqAppkey', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'WeChatAppId', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'WeChatAppkey', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'DiscordAppId', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'DiscordAppkey', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'MailruAppId', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'MailruAppkey', '');",
    "ALTER TABLE `story_seen` ADD `fingerPrint` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT 0 AFTER `user_id`, ADD INDEX (`fingerPrint`);",
    "CREATE TABLE `events_tickets` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `user_id` INT(11) NOT NULL DEFAULT '0' , `event_id` INT(11) NOT NULL DEFAULT '0' , `price` FLOAT(11) NOT NULL DEFAULT '0' , `commission` FLOAT(11) NOT NULL DEFAULT '0' , `final_price` FLOAT(11) NOT NULL DEFAULT '0' , `event_owner_id` INT(11) NOT NULL DEFAULT '0' , `time` INT(11) NOT NULL DEFAULT '0' , PRIMARY KEY (`id`), INDEX (`user_id`), INDEX (`event_id`), INDEX (`price`), INDEX (`commission`), INDEX (`final_price`), INDEX (`event_owner_id`), INDEX (`time`)) ENGINE = InnoDB;",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'event_commission', '0');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'youtube_import', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'youtube_video', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'youtube_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'spaces', 'off');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'space_name', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'spaces_key', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'spaces_secret', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'space_region', 'nyc3');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'size_issue', '');",
    "INSERT INTO `config` (`id`, `name`, `value`) VALUES (NULL, 'seo', '{\"ad-analytics\":{\"title\":\"Ad analytics\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"ads\":{\"title\":\"Ads\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"albums\":{\"title\":\"Albums\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"become\":{\"title\":\"Become\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"blogs\":{\"title\":\"Blogs\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"checkout\":{\"title\":\"Checkout\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"contact\":{\"title\":\"Contact\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"create-ads\":{\"title\":\"Create ads\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"create-article\":{\"title\":\"Create article\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"create-event\":{\"title\":\"Create event\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"create-product\":{\"title\":\"Create product\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"create_story\":{\"title\":\"Create story\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"customer_order\":{\"title\":\"Customer order\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"customer_orders\":{\"title\":\"Customer orders\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"dashboard\":{\"title\":\"Dashboard\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"discover\":{\"title\":\"Discover\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"edit-ad\":{\"title\":\"Edit ad\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"edit_event\":{\"title\":\"Edit event\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"edit_product\":{\"title\":\"Edit product\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"event\":{\"title\":\"Event\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"events\":{\"title\":\"Events\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"events_analytics\":{\"title\":\"Events analytics\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"explore-genres\":{\"title\":\"Explore genres\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"fame\":{\"title\":\"Fame\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"favourites\":{\"title\":\"Favourites\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"feed\":{\"title\":\"Feed\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"forgot-password\":{\"title\":\"Forgot password\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"genres\":{\"title\":\"Genres\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"go-pro\":{\"title\":\"Go pro\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"home\":{\"title\":\"Home\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"import\":{\"title\":\"Import\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"joined\":{\"title\":\"Joined\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"maintenance\":{\"title\":\"Maintenance\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"manage_events\":{\"title\":\"Manage events\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"manage_products\":{\"title\":\"Manage products\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"messages\":{\"title\":\"Messages\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"my_playlists\":{\"title\":\"My playlists\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"new_music\":{\"title\":\"New music\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"not-found\":{\"title\":\"Not found\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"order\":{\"title\":\"Order\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"orders\":{\"title\":\"Orders\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"payment-error\":{\"title\":\"Payment error\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"playlist\":{\"title\":\"Playlist\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"playlists\":{\"title\":\"Playlists\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"point-system\":{\"title\":\"Point system\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"product\":{\"title\":\"Product\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"purchase-album\":{\"title\":\"Purchase album\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"purchase\":{\"title\":\"Purchase\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"purchased\":{\"title\":\"Purchased\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"purchased_tickets\":{\"title\":\"Purchased tickets\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"recently_played\":{\"title\":\"Recently played\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"redirect\":{\"title\":\"Redirect\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"reset-password\":{\"title\":\"Reset password\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"search\":{\"title\":\"Search\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"settings\":{\"title\":\"Settings\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"site_pages\":{\"title\":\"Site pages\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"spotlight\":{\"title\":\"Spotlight\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"station\":{\"title\":\"Station\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"stations\":{\"title\":\"Stations\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"store\":{\"title\":\"Store\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"terms\":{\"title\":\"Terms\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"top-genres\":{\"title\":\"Top genres\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"top_music\":{\"title\":\"Top music\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"top_music_album\":{\"title\":\"Top music album\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"unusual-login\":{\"title\":\"Unusual login\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"upgraded\":{\"title\":\"Upgraded\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"upload-album\":{\"title\":\"Upload album\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"upload-single\":{\"title\":\"Upload single\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"upload-song\":{\"title\":\"Upload song\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"},\"user\":{\"title\":\"User\",\"meta_keywords\":\"deepsound,video sharing\",\"meta_description\":\"DeepSound is a PHP Audio Sharing Script, DeepSound is the best way to start your own audiosharing script!\"}}');",
    "ALTER TABLE `events` ADD `hash_id` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `id`, ADD INDEX (`hash_id`);",
    "ALTER TABLE `products` ADD `hash_id` VARCHAR(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' AFTER `id`, ADD INDEX (`hash_id`);",
    "ALTER TABLE `purchases` ADD `order_hash_id` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' AFTER `event_id`, ADD INDEX (`order_hash_id`);",
    "ALTER TABLE `activities` ADD `product_id` INT(11) NOT NULL DEFAULT '0' AFTER `track_id`, ADD INDEX (`product_id`);",
    "ALTER TABLE `activities` ADD `event_id` INT(11) NOT NULL DEFAULT '0' AFTER `product_id`, ADD INDEX (`event_id`);",
    "INSERT INTO `products_category` (`id`, `lang_key`) VALUES (NULL, 's_other');",
    "INSERT INTO `products_category` (`id`, `lang_key`) VALUES (NULL, 's_clothes');",
    "INSERT INTO `products_category` (`id`, `lang_key`) VALUES (NULL, 's_electronic');",
    "ALTER TABLE `purchases` ADD `title` VARCHAR(500) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '' AFTER `track_owner_id`, ADD INDEX (`title`);",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_your_address_has_been_added_successfully_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_your_address_has_been_edited_successfully_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_name_must_be_between_5_32_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_the_url_is_invalid._please_enter_a_valid_url_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_media_file_is_invalid._please_select_a_valid_image___video_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_media_file_is_invalid._please_select_a_valid_image_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_media_file_is_invalid._please_select_a_valid_audio_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_too_many_login_attempts_please_try_again_later_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_url_can_not_be_empty_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_address_can_not_be_empty_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_tickets_available_and_ticket_price_can_not_be_empty_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_event_cover_can_not_be_empty_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_event_video_can_not_be_empty_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_your_event_has_been_published_successfully_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_your_event_has_been_updated_successfully_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_payment_successfully_done_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_please_select_a_song_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_please_select_a_valid_image_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_your_product_has_been_published_successfully_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_your_product_is_under_review_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_your_product_has_been_edited_successfully_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_some_products_don_t_have_enough_of_units_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_you_don_t_have_enough_wallet_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_please_top_up_your_wallet_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_your_order_has_been_placed_successfully_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_tracking_info_has_been_saved_successfully_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_review_has_been_sent_successfully_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_your_request_is_under_review_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_your_story_has_been_published_successfully_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_your_story_has_been_uploaded_successfully_to_publish_it_please_pay_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_story_not_found_or_its_active_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, '_you_don_t_have_enough_money_please_top_up_your_wallet_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'login_with_linkedin');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'login_with_vkontakte');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'login_with_instagram');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'login_with_qq');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'login_with_wechat');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'login_with_discord');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'login_with_mailru');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_items_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'you_don_t_have_enough_wallet');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'please_top_up_your_wallet');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'add_new_address');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'create_new_event');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'manage_events');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'browse_events');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'joined_events');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'view_purchased_tickets');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_name');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_location');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'online');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'real_location');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_start_date');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_start_time');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_end_date');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_end_time');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'timezone');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'sell_tickets');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'tickets_available_total_tickets_available_for_this_event_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'ticket_price');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_description');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_cover');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_video_trailer');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'create_product');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'manage_products');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_item_units');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'related_to_song');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'images');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'who_can_see');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'show_to_my_followers_only');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'show_to_all_users');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'story_image');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'upload_song');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'shipped');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'delivered');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'payments');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'subtotal');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'refund_money');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'tracking_details');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'site_url');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'tracking_number');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'delivery_address');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_orders_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'products');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'view_details');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'stories');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'joined');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'join');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'buy_a_ticket');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'view_trailer');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'edit_event');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'start_date');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'end_date');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'available_tickets');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'joined_people');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'location');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_events');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_joined_users');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_available_tickets');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'most_joined_events');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'most_sold_events');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_more_events_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_more_tickets_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_more_products_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_more_reviews_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'payment_successfully_done');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'are_you_sure_you_want_to_pay_to_buy_song_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'are_you_sure_you_want_to_pay_to_buy_album_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'are_you_sure_you_want_to_pay_to_upgrade_to_pro_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'you_don_t_have_enough_money_please_top_up_your_wallet');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'interested');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_more_views');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'are_you_sure_you_want_to_delete_your_story_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'please_add_a_new_address');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'please_select_address');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'refund');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'create_event');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'checkout');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'store_orders');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'my_orders');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_request_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'delete_event');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'cashfree');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'paystack');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'razorpay');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'paysera');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'iyzipay');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'payu');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'securionpay');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'authorize');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'placed');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'canceled');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'packed');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'commission');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'final_price');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'link');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'site_commission');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'currently_unavailable.');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'write_review');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'photos');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'verified_purchase');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'events');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'my_addresses');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'add_new');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'edit_address');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'postcode___zip');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'invitation_links');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'available_links');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'generated_links');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'used_links');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'generate_link');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'invited_user');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'date');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'copy');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'copied');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'available_wallet');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'top_up_wallet');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'hall_of_fame');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'analytics');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'more_info');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'listen_in_youtube');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'tagged_artists');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'donate');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 's_other');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 's_clothes');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 's_electronic');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'remove_from_cart');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'add_to_cart');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_cart_is_empty.');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'delete_your_address');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'are_you_sure_you_want_to_delete_this_address_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'payment_alert');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'are_you_sure_you_want_to_pay_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'delete_your_product');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'are_you_sure_you_want_to_delete_this_product_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'pay_for_story');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'are_you_sure_you_want_to_pay_for_create_story_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'pay_from_wallet');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'are_you_sure_you_want_to_buy_a_ticket_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'leave_event');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'are_you_sure_you_want_to_leave_this_event_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'leave');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'delete_your_event');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'are_you_sure_you_want_to_delete_this_event_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'get_verified___sell_your_songs___sell_products___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'get_verified___sell_your_songs___create_events_and_sell_tickets___get_a_special_looking_profile_and_get_famous_on_our_platform_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'get_verified___sell_your_songs___sell_products___get_a_special_looking_profile_and_get_famous_on_our_platform_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'get_verified___sell_your_songs___get_a_special_looking_profile_and_get_famous_on_our_platform_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_events_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'product');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'donate_button');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'my_information');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'please_choose_which_information_you_would_like_to_download');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'generate_file');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_event_has_been_published_successfully');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_tickets_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'purchased_tickets');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_event_has_been_updated_successfully');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_product_is_under_review');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_product_has_been_published_successfully');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'edit_product');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_product_has_been_edited_successfully');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'guest');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'ticket');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'events_analytics');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'id');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'tag_artists');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'tag_other_artists_to_show_they_performed_together');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'download_ticket');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_order_has_been_placed_successfully');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'order');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'sale_invoice');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'seller_name');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'seller_email');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'invoice_to');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'payment_details');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_due');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'bank_name');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'item');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'download_invoice');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'details');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_products_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_reviews_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'you_are_about_to_purchase_the_items__do_you_want_to_proceed_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'request_a_refund');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'new_orders_has_been_placed');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'order_status_has_been_changed');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_refund_request_has_been_declined');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_refund_request_has_been_approved_your_money_added_to_your_wallet');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'added_tracking_info');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_product_has_been_approved');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'joined_your_event');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'bought_a_ticket');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'orders');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'you_are_not_purchased');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'order_not_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'if_the_order_status_wasn_t_set_to_delivered_within_60_days_from_the_order_date__it_will_be_automatically_be_sent_to__delivered_.');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'if_the_order_wasn_t_actually_delivered__the_buyer_can_request_a_refund.');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_request_is_under_review');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'request');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'please_explain_the_reason');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'top_products');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'best_selling_songs___products_this_week');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'best_selling_songs___albums_this_week');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'accepted_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'please_wait__this_may_take_few_minutes.');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'instead_of_uploading_a_song__you_can_easily_import_songs_using');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'imported_a_new_song_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'review_has_been_sent_successfully');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'created_new_product_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'created_new_event_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'joined_new_event_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'purchased_a_ticket_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'my_store');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'store_analytics');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_products');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_earned');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_commission');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'total_sub_earned');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'most_sold_products');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_name_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_description_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'start_date_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'create_story');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'product_related_song_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'product_info');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_info');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'you_are_not_the_owner');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_not_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'this_event_is_free');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'there_is_no_available_tickets');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'card_is_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'address_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'id_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_location_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'start_time_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'end_date_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'end_time_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'timezone_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_image_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'product_title_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'product_description_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'product_tags_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'product_price_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'product_units_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'product_category_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'product_image_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'product_not_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'address_not_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'tracking_url_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'tracking_number_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'please_enter_a_valid_url');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'rating_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'review_can_not_be_empty');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'please_who_can_see_the_story');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'please_select_a_story_image');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'please_select_a_story_song');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'story_not_found_or_its_not_active');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'get_verified');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'sell_your_songs');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'sell_products');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'create_events_and_sell_tickets');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'upload_more_songs');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'get_more_space');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'get_a_special_looking_profile_and_get_famous_on_our_platform_');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'ticket_was_purchased_in_sitename__date');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'created_new_product');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'track');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_ticket');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'for');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_starts');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_ends');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'video_duration_must_be_less_than_or_equal_10_seconds');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'purchased_by');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'event_address');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'no_more_orders_found');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'login_to_purchase');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_video_will_be_converted_to_mp3_soon__you_ll_get_notified_once_imported');",
    "INSERT INTO `langs` (`id`, `lang_key`) VALUES (NULL, 'your_song_is_ready_to_view');",
    "ALTER TABLE `langs` CHANGE `options` `options` VARCHAR(120) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '';",
    "UPDATE `config` SET value = '1.4' WHERE name = 'version';"

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