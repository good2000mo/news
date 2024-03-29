<?php

$lang_admin_settings = array(

'Settings updated'				=>	'設定已更新。',

// Setup section
'Setup personal'				=>	'個性化您安裝的 PunBB 論壇',
'Setup personal legend'			=>	'配置 PunBB 論壇',
'Board description label'		=>	'論壇描述',
'Board title label'				=>	'論壇名稱',
'Default style label'			=>	'預設風格',
'Setup local'					=>	'配置 PunBB 符合本地設定',
'Setup local legend'			=>	'本地設定',
'Default timezone label'		=>	'預設時區',
'Adjust for DST'				=>	'調整日光節約時間',
'DST label'						=>	'使日光節約時間生效 (向前提早一小時)。',
'Default language label'		=>	'預設語系',
'Default language help'			=>	'(如果您移除了某個語系套件，您必須更新這裡的設定)',
'Time format label'				=>	'時間格式',
'Date format label'				=>	'日期格式',
'Current format'				=>	'[ 目前格式: %s ] %s',
'External format help'			=>	'查閱 <a class="exthelp" href="http://www.php.net/manual/en/function.date.php">這裡</a> 來獲知格式選項。',
'Setup timeouts'				=>	'預設訪問逾時和頁面重新導向時間',
'Setup timeouts legend'			=>	'預設逾時',
'Visit timeout label'			=>	'訪問逾時',
'Visit timeout help'			=>	'會員登出論壇多少秒後，才更新他/她最後訪問的時間資料。',
'Online timeout label'			=>	'線上逾時',
'Online timeout help'			=>	'會員訪問論壇閒置多少秒後，才從線上會員清單裡移除。',
'Redirect time label'			=>	'頁面導向時間',
'Redirect time help'			=>	'當頁面做重新導向時所需等待的秒數。如果設為 0, 不會顯示導向頁面。(不建議設為 0)',
'Setup pagination'				=>	'預設主題、文章以及文章預覽分頁顯示',
'Setup pagination legend'		=>	'分頁顯示預設',
'Topics per page label'			=>	'每頁顯示幾則主題',
'Posts per page label'			=>	'每頁顯示幾篇文章',
'Topic review label'			=>	'主題檢閱',
'Topic review help'				=>	'最新的主題排在最前面。輸入 0 表示不啟用本功能。',
'Setup reports'					=>	'主題和文章檢舉報告的接收方式',
'Setup reports legend'			=>	'接收檢舉報告',
'Reporting method'				=>	'報告方式',
'Report internal label'			=>	'用內部的報告系統。',
'Report both label'				=>	'同時使用內部的報告系統和電子郵件的方式寄往郵件清單裡的地址。',
'Report email label'			=>	'用電子郵件寄往郵件清單裡的地址。',
'Setup URL'						=>	'為您的論壇頁面設定網址顯示方案 (<abbr title ="Search Engine Friendly">SEF</abbr> URLs)',
'Setup URL legend'				=>	'選擇方案',
'URL scheme info'				=>	'<strong>警告！</strong> 假如您選擇其他方案而不是預設方案，您必須從 extras 目錄裡複製並且上傳 .htaccess 檔到安裝論壇的根目錄裡。存放論壇程式的伺服主機必須配置支援 mod_rewrite 模組並且允許使用 .htaccess 檔。使用 Apache 以外的網頁伺服器者請參閱您使用的伺服器文件說明。',
'URL scheme label'				=>	'網址方案',
'URL scheme help'				=>	'請確認您已經閱讀並且了解上面所提的資訊。',
'Setup links'					=>	'新增您自己的連結到論壇的導覽列選單',
'Setup links info'				=>	'藉由在這個文字區塊裡輸入 HTML 超連結，任何數量的連結項目將會被加進每頁頁首裡的導覽選單列。新增連結項目的格式為 X = &lt;a href="連結網址"&gt;項目名稱&lt;/a&gt;，X 代表這個連結項目要安插的位置 (舉例來說 0 代表安插在導覽列最前頭，而 2 代表安插在導覽列裡的 "會員列表" 後面)。每個連結項目間以斷行來做區隔。',
'Setup links legend'			=>	'選單項目',
'Enter links label'				=>	'輸入您的連結',
'Error no board title'			=>	'您必須輸入一個論壇名稱。',
'Error timeout value'			=>	'"線上逾時" 的值必須小於 "訪問逾時" 的值。',


// Features section
'Features general'				=>	'可選的一般功能',
'Features general legend'		=>	'一般功能',
'Searching'						=>	'資料搜尋',
'Search all label'				=>	'允許會員搜尋所有的討論版面而不是一次只能搜尋一個。如果是因為過多的搜尋動作使得伺服主機負載過大，可以選擇取消本功能。',
'User ranks'					=>	'會員等級',
'User ranks label'				=>	'啟用依發表文章數決定會員等級功能。',
'Censor words'					=>	'文字過濾',
'Censor words label'			=>	'啟用過濾指定文字功能。',
'Quick jump'					=>	'快速連結選單',
'Quick jump label'				=>	'啟用論壇頁尾的快速連結 (跳往其他版面) 下拉式選單功能。',
'Show version'					=>	'顯示版本號碼',
'Show version label'			=>	'在頁尾顯示 PunBB 版本號碼。',
'Online list'					=>	'線上會員清單',
'Users online label'			=>	'顯示目前在線上瀏覽本論壇的訪客和註冊會員清單。',
'Features posting'				=>	'主題與文章功能和資訊',
'Features posting legend'		=>	'發文功能',
'Quick post'					=>	'快速回覆',
'Quick post label'				=>	'在每篇主題頁尾新增一個快速回覆的表單區塊。',
'Subscriptions'					=>	'訂閱主題',
'Subscriptions label'			=>	'允許會員訂閱主題 (當有人回覆文章時接收電子郵件通知)。',
'Guest posting'					=>	'訪客發文',
'Guest posting label'			=>	'訪客張貼文章時必須填上電子郵件。',
'User has posted'				=>	'會員參與主題',
'User has posted label'			=>	'如果會員曾在某篇主題裡發表過文章，那麼這項功能會在該主題前加上一個圓點記號當作提示。如果您覺得開啟這項功能會造成您的伺服主機產生過多的負載，請取消這項功能。如果使用SQLite請忽略這個項目。',
'Topic views'					=>	'主題點閱數',
'Topic views label'				=>	'持續追蹤主題的點閱數。如果您覺得開啟這項功能會造成您的伺服主機產生過多的負載，請取消這項功能。',
'User post count'				=>	'會員文章篇數計算',
'User post count label'			=>	'在文章中、個人資料，和會員列表中顯示會員發表的文章篇數。',
'User info'						=>	'顯示會員資訊於文章中',
'User info label'				=>	'在文章中顯示發文者居住地、註冊日期、文章篇數、電子郵件，以及個人網頁資訊。',
'Features posts'				=>	'主題與文章內容',
'Features posts legend'			=>	'主題與文章內容選項',
'Post content group'			=>	'文章內容選項',
'Allow BBCode label'			=>	'允許在文章中使用 BBCode 標籤(建議使用)。',
'Allow img label'				=>	'允許在文章中使用 BBCode [img] 圖片連結標籤。',
'Smilies in posts label'		=>	'在文章中轉換表情符號為小圖示。',
'Make clickable links label'	=>	'偵測任何於文章中發現的連結，並使它變成可供點選的超連結。',
'Allow capitals group'			=>	'允許全部大寫',
'All caps message label'		=>	'允許在文章中全部使用大寫英文字母來書寫。',
'All caps subject label'		=>	'允許在標題中全部使用大寫英文字母來書寫。',
'Indent size label'				=>	'[code] 標籤縮排大小',
'Indent size help'				=>	'使用空白做文字縮排。如果設值為 8，將使用固定長度的跳格距離來做縮排。',
'Quote depth label'				=>	'[quote] 標籤深度',
'Quote depth help'				=>	'[quote] 標籤裡包含其他 [quote] 標籤的最大數量，任何超過這個數量的標籤將被排除。',
'Features sigs'					=>	'會員個人簽名與簽名內容',
'Features sigs legend'			=>	'個人簽名選項',
'Allow signatures'				=>	'允許個人簽名',
'Allow signatures label'		=>	'允許會員在其文章中使用個人簽名。',
'Signature content group'		=>	'個人簽名內容',
'BBCode in sigs label'			=>	'允許在個人簽名中使用 BBCode 標籤。',
'Img in sigs label'				=>	'允許在個人簽名中使用 BBCode [img] 圖片連結標籤 (不建議使用)。',
'All caps sigs label'			=>	'允許在個人簽名中全部使用大寫英文字母來書寫。',
'Smilies in sigs label'			=>	'在個人簽名中轉換表情符號為小圖示。',
'Max sig length label'			=>	'個人簽名字元數',
'Max sig lines label'			=>	'個人簽名行數',
'Features Avatars'				=>	'會員頭像 (上傳與尺寸大小設定)',
'Features Avatars legend'		=>	'會員頭像設定',
'Allow avatars'					=>	'允許個人頭像',
'Allow avatars label'			=>	'允許會員可以上傳他們自己的頭像，用來顯示在他們的文章裡。',
'Avatar directory label'		=>	'頭像上傳目錄',
'Avatar directory help'			=>	'相對於 PunBB 的根目錄。PHP 必須擁有這個目錄的寫入權限。',
'Avatar Max width label'		=>	'頭像最大寬度',
'Avatar Max width help'			=>	'像素 (建議使用 60 像素)。',
'Avatar Max height label'		=>	'頭像最大高度',
'Avatar Max height help'		=>	'像素 (建議使用 60 像素)。',
'Avatar Max size label'			=>	'頭像最大容量',
'Avatar Max size help'			=>	'Bytes (建議使用 10240 bytes)。',
'Features update'				=>	'自動檢查更新',
'Features update info'			=>	'PunBB 可以定期檢查是否有任何重要的更新可供您使用。這個更新可能是新的版本釋出或是修正程式。當有更新的程式可以使用時，將會在論壇上方顯示訊息通知論壇管理員。',
'Features update disabled info'	=>	'自動檢查更新的功能已經關閉。為了要支援這項功能，PunBB 所使用的 PHP 環境必須支援 <a href="http://www.php.net/manual/en/ref.curl.php">cURL 延伸模組</a>，<a href="http://www.php.net/manual/en/function.fsockopen.php">fsockopen() 函式</a> 或者必須配置啟用 <a href="http://www.php.net/manual/en/ref.filesystem.php#ini.allow-url-fopen">allow_url_fopen</a> 功能。',
'Features update legend'		=>	'自動更新',
'Update check'					=>	'檢查更新',
'Update check label'			=>	'啟用自動檢查更新。',
'Check for versions'			=>	'檢查新的版本',
'Auto check for versions'		=>	'啟用檢查新的延伸模組版本。',
'Features gzip'					=>	'使用 GZip 壓縮輸出',
'Features gzip legend'			=>	'網頁內容壓縮後輸出',
'Features gzip info'			=>	'如果開啟本功能，PunBB 將會把頁面輸出壓縮後再傳給瀏覽器來顯示。這項功能的好處是可以減少頻寬的使用量，但是會多使用一些 CPU 效能。本功能需要 PHP 有設定支援 zlib (--with-zlib) 時才有作用。注意︰如果您已經使用 Apache 的 mod_gzip 模組或者是 mod_deflate 模組來壓縮 PHP 程式的話，您應該取消使用本功能。',
'Enable gzip'					=>	'啟用 GZip',
'Enable gzip label'				=>	'啟用頁面輸出使用 Gzip 壓縮。',

// Announcements section
'Announcements head'			=>	'在討論區中所有頁面裡顯示公告',
'Announcements legend'			=>	'公告',
'Enable announcement'			=>	'啟用公告訊息',
'Enable announcement label'		=>	'顯示公告訊息。',
'Announcement heading label'	=>	'公告標題',
'Announcement message label'	=>	'公告訊息',
'Announcement message help'		=>	'您可以在公告訊息中使用 HTML 語法來撰寫。公告訊息不會採用像論壇文章一樣的解析方式。',
'Announcement message default'	=>	'<p>在此輸入您的公告。</p>',

// Registration section
'Registration new'				=>	'新會員註冊',
'New reg info'					=>	'您可以選擇是否要啟用驗證註冊功能。當啟用本功能時，會員在註冊後會收到一封內含啟用連結的電子郵件。他們可以藉此設定他們的密碼以及進行登入。這項驗證功能在會員更換新的電子郵件地址時同樣需要再做一次驗證。這是有效避免濫用註冊和確認會員資料中所填寫的電子郵件的正確性。',
'Registration new legend'		=>	'新會員註冊設定',
'Allow new reg'					=>	'允許新會員註冊',
'Allow new reg label'			=>	'控制本論壇是否繼續接受新會員註冊。如果您有特別需求可以關閉會員註冊功能。',
'Verify reg'					=>	'驗證註冊',
'Verify reg label'				=>	'藉由電子郵件驗證所有新註冊的會員。',
'Reg e-mail group'				=>	'註冊電子郵件',
'Allow banned label'			=>	'允許使用被封鎖的電子郵件地址註冊。',
'Allow dupe label'				=>	'允許重覆使用同一個電子郵件地址註冊。',
'Report new reg'				=>	'電子郵件通知',
'Report new reg label'			=>	'當有新會員在論壇註冊的時候，發函通知郵件清單裡的電子郵件地址。',
'E-mail setting group'			=>	'預設電子郵件設定',
'Display e-mail label'			=>	'對其他會員顯示電子郵件地址。',
'Allow form e-mail label'		=>	'隱藏電子郵件地址，但是允許使用論壇的寄信功能寄信。',
'Disallow form e-mail label'	=>	'隱藏電子郵件地址，也不允許使用論壇的寄信功能寄信。',
'Registration rules'			=>	'論壇規則 (啟用及建立論壇規則)',
'Registration rules info'		=>	'您可以要求當新會員在註冊時必須同意本論壇所設定的規則。論壇規則將會以連結的方式顯示在論壇上方的導覽列裡。您可以在下列選擇是否要啟用論壇規則以及撰寫論壇規則的內容。',
'Registration rules legend'		=>	'論壇規則',
'Require rules'					=>	'使用論壇規則',
'Require rules label'			=>	'要求新會員在註冊時必須同意本論壇所設定的規則。',
'Compose rules label'			=>	'撰寫規則',
'Compose rules help'			=>	'您可以使用 HTML 語法來撰寫，這段文字訊息不會採用像論壇文章一樣的解析方式。保持空白的話這項功能將會被取消',
'Rules default'					=>	'在這裡撰寫你的規則。',

// Email section
'E-mail addresses'				=>	'論壇電子郵件地址與郵件清單',
'E-mail addresses legend'		=>	'電子郵件地址',
'Admin e-mail'					=>	'論壇管理員電子郵件',
'Webmaster e-mail label'		=>	'網站管理員電子郵件',
'Webmaster e-mail help'			=>	'由論壇發送的電子郵件皆設定由網站管理員電子郵件當作寄件者發出。',
'Mailing list label'			=>	'建立郵件清單',
'Mailing list help'				=>	'用逗號來區隔清單中的電子郵件地址。清單裡所列的郵件地址將會收到檢舉報告和新註冊會員通知。',
'E-mail server'					=>	'配置論壇發信用的郵件伺服器',
'E-mail server legend'			=>	'郵件伺服器',
'E-mail server info'			=>	'在大部分的情況下 PunBB 可以使用本機郵件程式來寄送電子郵件，您通常可以忽略下列的設定。PunBB 也可以設定使用外部的郵件主機。您可以輸入外部郵件主機的位址，有需要的話，如果這個 SMTP 主機不是使用預設的埠號 25，您也可以自行指定所需要的埠號 (例如: mail.example.com:3580)。',
'SMTP address label'			=>	'SMTP 主機位址',
'SMTP address help'				=>	'使用外部 SMTP 主機來寄送電子郵件。保持本欄空白表示使用本機郵件程式來寄送。',
'SMTP username label'			=>	'SMTP 使用者帳號',
'SMTP username help'			=>	'如果您使用的 SMTP 主機需要做驗證才能寄信，請於此欄輸入使用者帳號。',
'SMTP password label'			=>	'SMTP 使用者密碼',
'SMTP password help'			=>	'如果您使用的 SMTP 主機需要做驗證才能寄信，請於此欄輸入使用者密碼。',
'SMTP SSL'						=>	'SMTP 使用 SSL 加密協定',
'SMTP SSL label'				=>	'如果您使用的 SMTP 主機需要使用且您使用的 PHP 版本支援 SSL 功能，可以啟用 SMTP 連線使用 SSL 加密。',
'Error invalid admin e-mail'	=>	'您輸入的論壇管理員電子郵件不正確。',
'Error invalid web e-mail'		=>	'您輸入的網站管理員電子郵件不正確。',

// Maintenance section
'Maintenance head'				=>	'設定論壇維護訊息與啟用論壇維護模式',
'Maintenance mode info'			=>	'<strong>重要！</strong> 使論壇進入維護模式意指整個論壇將只有論壇管理員能夠操作。當您的論壇需要暫時性休息以進行維護時，應該啟用這項功能。',
'Maintenance mode warn'			=>	'<strong>警告！</strong> 當您處在論壇維護模式底下時請不要做登出的動作，否則您將可能無法再登入進來。',
'Maintenance legend'			=>	'論壇維護',
'Maintenance mode'				=>	'論壇維護',
'Maintenance mode label'		=>	'使論壇進入維護模式。',
'Maintenance message label'		=>	'論壇維護訊息',
'Maintenance message help'		=>	'這段訊息將會在論壇模式啟用時顯示給來訪者觀看。您可以使用系統提供的預設訊息或是重寫新的訊息。在訊息裡可以使用 HTML 語法。',
'Maintenance message default'	=>	'本論壇目前進行維護中，暫時停止服務。請稍後再來拜訪。<br /><br />論壇管理員',

);
