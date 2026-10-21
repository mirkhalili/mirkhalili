-- =====================================================================
--  اسکیمای دیتابیس — میر خلیلی CMS (QAVNS 011.2.6.0)
--  موتور: MySQL / MariaDB — charset: utf8mb4 (پشتیبانی کامل فارسی)
--  اجرا: برای نصب تازه از phpMyAdmin (Import)؛ برای ارتقا migration نسخه‌دار را اجرا کنید
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------- کاربران ادمین ----------
CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username VARCHAR(50) NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  display_name VARCHAR(100) NOT NULL DEFAULT '',
  email VARCHAR(180) NOT NULL DEFAULT '',
  role ENUM('admin','editor','viewer') NOT NULL DEFAULT 'admin',
  is_active TINYINT(1) NOT NULL DEFAULT 1,
  last_login TIMESTAMP NULL DEFAULT NULL,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_users_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- تنظیمات سایت (کلید/مقدار) ----------
CREATE TABLE IF NOT EXISTS settings (
  setting_key VARCHAR(60) NOT NULL,
  setting_value TEXT NULL,
  PRIMARY KEY (setting_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- شبکه‌های اجتماعی ----------
CREATE TABLE IF NOT EXISTS socials (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  label VARCHAR(50) NOT NULL,
  url VARCHAR(255) NOT NULL,
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- دسته‌بندی نمونه‌کارها ----------
CREATE TABLE IF NOT EXISTS categories (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  slug VARCHAR(80) NOT NULL,
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_categories_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- پروژه‌ها ----------
CREATE TABLE IF NOT EXISTS projects (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  slug VARCHAR(120) NOT NULL,
  title VARCHAR(180) NOT NULL,
  title_en VARCHAR(180) NOT NULL DEFAULT '',
  category_id INT UNSIGNED NULL,
  year_fa VARCHAR(20) NOT NULL DEFAULT '',
  year_en VARCHAR(10) NOT NULL DEFAULT '',
  client VARCHAR(180) NOT NULL DEFAULT '',
  role_fa VARCHAR(120) NOT NULL DEFAULT '',
  excerpt TEXT NULL,
  description TEXT NULL,
  cover VARCHAR(255) NOT NULL DEFAULT '',
  specimen_label VARCHAR(120) NOT NULL DEFAULT '',
  specimen_sample VARCHAR(120) NOT NULL DEFAULT '',
  sort INT NOT NULL DEFAULT 0,
  published TINYINT(1) NOT NULL DEFAULT 1,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_projects_slug (slug),
  KEY idx_projects_category (category_id),
  CONSTRAINT fk_projects_category FOREIGN KEY (category_id)
    REFERENCES categories (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- خدمات هر پروژه ----------
CREATE TABLE IF NOT EXISTS project_services (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  project_id INT UNSIGNED NOT NULL,
  label VARCHAR(150) NOT NULL,
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_pservices_project (project_id),
  CONSTRAINT fk_pservices_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- پالت رنگ هر پروژه ----------
CREATE TABLE IF NOT EXISTS project_palette (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  project_id INT UNSIGNED NOT NULL,
  hex VARCHAR(9) NOT NULL,
  name VARCHAR(60) NOT NULL DEFAULT '',
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_ppalette_project (project_id),
  CONSTRAINT fk_ppalette_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- معیارهای هر پروژه ----------
CREATE TABLE IF NOT EXISTS project_metrics (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  project_id INT UNSIGNED NOT NULL,
  value VARCHAR(30) NOT NULL,
  label VARCHAR(120) NOT NULL DEFAULT '',
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_pmetrics_project (project_id),
  CONSTRAINT fk_pmetrics_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- گالری تصاویر هر پروژه ----------
CREATE TABLE IF NOT EXISTS project_images (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  project_id INT UNSIGNED NOT NULL,
  path VARCHAR(255) NOT NULL,
  caption VARCHAR(180) NOT NULL DEFAULT '',
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_pimages_project (project_id),
  CONSTRAINT fk_pimages_project FOREIGN KEY (project_id)
    REFERENCES projects (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- خدمات استودیو ----------
CREATE TABLE IF NOT EXISTS services (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(150) NOT NULL,
  title_en VARCHAR(150) NOT NULL DEFAULT '',
  description TEXT NULL,
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS service_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  service_id INT UNSIGNED NOT NULL,
  label VARCHAR(150) NOT NULL,
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_sitems_service (service_id),
  CONSTRAINT fk_sitems_service FOREIGN KEY (service_id)
    REFERENCES services (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- مراحل فرآیند کار ----------
CREATE TABLE IF NOT EXISTS process_steps (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  title VARCHAR(120) NOT NULL,
  description TEXT NULL,
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- آمار ----------
CREATE TABLE IF NOT EXISTS stats (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  value VARCHAR(20) NOT NULL,
  label VARCHAR(80) NOT NULL,
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- مسیر حرفه‌ای (رزومه) ----------
CREATE TABLE IF NOT EXISTS timeline (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  period VARCHAR(60) NOT NULL,
  title VARCHAR(150) NOT NULL,
  description TEXT NULL,
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- جوایز ----------
CREATE TABLE IF NOT EXISTS awards (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  year_fa VARCHAR(20) NOT NULL,
  title VARCHAR(180) NOT NULL,
  project VARCHAR(180) NOT NULL DEFAULT '',
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- مهارت‌ها / ابزارها ----------
CREATE TABLE IF NOT EXISTS skills (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(60) NOT NULL,
  sort INT NOT NULL DEFAULT 0,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- پیام‌های فرم تماس ----------
CREATE TABLE IF NOT EXISTS messages (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(180) NOT NULL,
  project_type VARCHAR(60) NOT NULL DEFAULT '',
  body TEXT NOT NULL,
  is_read TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------- رویدادنگاری عملیات پنل ----------
CREATE TABLE IF NOT EXISTS admin_activity (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  actor_id INT UNSIGNED NULL,
  action VARCHAR(40) NOT NULL,
  entity_type VARCHAR(50) NOT NULL,
  entity_id BIGINT UNSIGNED NULL,
  description VARCHAR(255) NOT NULL DEFAULT '',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_admin_activity_created (created_at),
  KEY idx_admin_activity_actor (actor_id),
  CONSTRAINT fk_admin_activity_actor FOREIGN KEY (actor_id)
    REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- بازدیدهای ناشناس صفحات عمومی (بدون IP و user-agent) ----------
CREATE TABLE IF NOT EXISTS site_visits (
  id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  path VARCHAR(255) NOT NULL,
  referrer_host VARCHAR(190) NOT NULL DEFAULT '',
  visited_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY idx_site_visits_date (visited_at),
  KEY idx_site_visits_path_date (path, visited_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- =====================================================================
--  داده‌های اولیه (Seed)
-- =====================================================================

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'میر خلیلی'),
('site_name_en', 'Mir Khalili'),
('role', 'طراح گرافیک و هویت بصری'),
('city', 'تهران، ایران'),
('email', 'studio@mirkhalili.com'),
('phone', '+۹۸ ۲۱ ۸۸۰۰ ۰۰۰'),
('availability', 'در دسترس برای پروژه‌های جدید'),
('hero_l1', 'طراحی که دیده'),
('hero_l2', 'می‌شود،'),
('hero_l3', 'می‌شود.'),
('hero_intro', 'سیستم‌های بصری دقیق، تایپوگرافی فارسی درست و چاپی که روی کاغذ همان‌قدر خوب است که روی صفحه.'),
('hero_areas', 'هویت بصری و برندینگ|چاپ، نشر و بسته‌بندی|تایپوگرافی و حروف‌چینی فارسی'),
('marquee', 'هویت بصری|پوستر و چاپ|کتاب و نشر|کارت ویزیت|بنر شهری|تایپوگرافی فارسی'),
('studio_title', 'وسواس روی جزئیات، احترام به مخاطب'),
('studio_text', 'استودیوی من یک میز، یک گرید و کلی کاغذ باطله است. باور دارم طراحی خوب از محدودیت می‌آید: پالت کوتاه، تایپوگرافی دقیق و ایده‌ای که بتوان آن را در یک جمله گفت. بقیه‌اش حذف کردن است.'),
('about_title', 'طراح گرافیکی که هنوز با مداد شروع می‌کند و با گرید تمام می‌کند.'),
('about_intro', 'من میر خلیلی هستم؛ طراح گرافیک و هویت بصری. کارم ساختن نظم بصری برای برندهایی است که حرف مهمی برای گفتن دارند.'),
('about_p1', 'از ۱۳۹۲ وارد حرفه شدم؛ از پوسترهای فروشگاهی شروع کردم و به هویت‌های ملی رسیدم. در این مسیر یک چیز عوض نشد: باور به اینکه طراحی خوب اول مسئله را حل می‌کند و بعد دیده می‌شود — نه برعکس.'),
('about_p2', 'روش کار من ساده است: پژوهش جدی، اسکچ زیاد، حذف بی‌رحم. هر پروژه یک گرید دارد، یک پالت کوتاه و یک ایدهٔ قابل گفتن در یک جمله. اگر ایده در یک جمله جا نشود، هنوز ایده نیست.'),
('about_p3', 'خارج از پروژه‌ها، روی تایپ‌فیس فارسی کار می‌کنم و هر سال یک پروژهٔ شخصی چاپی منتشر می‌کنم؛ چون کاغذ، سخت‌گیرترین مشتری دنیاست.'),
('services_intro', 'هر پروژه با یک گفت‌وگوی صادقانه شروع می‌شود: مسئله چیست، بودجه چقدر است و موفقیت یعنی چه. بعد از آن، یکی از این مسیرها را با هم طی می‌کنیم.'),
('process_note', 'برای پروژه‌های هویت بصری، معمولاً بین ۶ تا ۱۲ هفته زمان لازم است؛ برای بسته‌بندی و چاپ، بسته به تیراژ و فینیشینگ، بین ۴ تا ۸ هفته.'),
('contact_note', 'شنبه تا چهارشنبه، ۱۰ تا ۱۸ به وقت تهران. معمولاً کمتر از ۲۴ ساعت جواب می‌دهم.');

INSERT INTO socials (label, url, sort) VALUES
('Behance', 'https://behance.net/mirkhalili', 1),
('Instagram', 'https://instagram.com/mirkhalili.studio', 2),
('Dribbble', 'https://dribbble.com/mirkhalili', 3),
('LinkedIn', 'https://linkedin.com/in/mirkhalili', 4);

INSERT INTO categories (name, slug, sort) VALUES
('هویت بصری و لوگو', 'logo-identity', 1),
('پوستر', 'poster', 2),
('کتاب و نشر', 'book', 3),
('بسته‌بندی', 'packaging', 4),
('کارت ویزیت', 'business-card', 5),
('بنر و بیلبورد', 'banner', 6),
('تایپوگرافی', 'typography', 7);

INSERT INTO projects
(slug, title, title_en, category_id, year_fa, year_en, client, role_fa, excerpt, description, cover, specimen_label, specimen_sample, sort) VALUES
('rust-coffee-identity', 'رُست — هویت بصری کافه و رستری', 'Rost Coffee Roastery', 1, '۱۴۰۴', '2025', 'رُست، رستری تخصصی قهوه', 'مدیر هنری و طراح ارشد',
 'یک سیستم هندسی سخت‌گیرانه برای رستری‌ای که قهوه را مثل یک مادهٔ معماری جدی می‌گیرد.',
 'رُست می‌خواست از فضای کافه‌های قهوه‌ایِ دست‌ساز فاصله بگیرد و قهوه را به‌عنوان یک مادهٔ دقیق صنعتی معرفی کند. نقطهٔ شروع، یک شبکهٔ شش‌خانه‌ای بود که همهٔ عناصر برند — از نشان تا بسته‌بندی — از آن مشتق می‌شوند.\n\nنشان برند از تقاطع فرم دانهٔ قهوه با دایرهٔ مهر ساخته شد؛ مهری که روی هر بسته حک می‌شود و اصالت برشته‌کاری را روایت می‌کند. پالت رنگی عمداً محدود ماند: مرکب، کاغذ خام و یک نارنجیِ آجری که فقط برای اطلاعات ضروری خرج می‌شود.\n\nدر پایان، یک راهنمای ۴۸صفحه‌ای تحویل شد که چیدمان بسته‌ها، سلسله‌مراتب تایپوگرافی فارسی و لاتین و قواعد کاربرد مهر را مشخص می‌کند؛ تا هویت در همهٔ تماس‌ها یک‌صدا بماند.',
 '/assets/uploads/coffee-identity.jpg', 'نمایش حروف بسته‌بندی', 'برشته‌کاری دقیق', 1),
('tehran-short-film-posters', 'جشنوارهٔ فیلم کوتاه — مجموعه پوستر', 'Tehran Short Film Festival', 2, '۱۴۰۳', '2024', 'جشنوارهٔ بین‌المللی فیلم کوتاه تهران', 'طراح پوستر و آرت دیرکتور',
 'سه پوستر، یک زبان: فرم‌های حرف‌مانند که مثل فریم‌های فیلم روی هم برش می‌خورند.',
 'برای این دوره از جشنواره، به‌جای تصویر صحنه‌ای، خودِ فریم قهرمان شد: مستطیل‌هایی که مثل کادر دوربین برش می‌خورند و از دل‌شان فرم‌های انتزاعیِ حرف بیرون می‌زند.\n\nمجموعهٔ سه‌گانه با ریزوگراف دو رنگ چاپ شد تا بافت دانه‌دار کاغذ، لرزشِ عمدیِ تصویر سینمایی را تداعی کند. هر پوستر به‌تنهایی کامل است و در کنار هم، یک تیزر متحرکِ چاپی می‌سازند.\n\nاین مجموعه در نمایشگاه جنبی جشنواره به‌صورت نصب دیواری اجرا شد و نسخهٔ محدود ۲۰۰تایی آن برای مجموعه‌داران چاپ گردید.',
 '/assets/uploads/festival-posters.jpg', 'تایپوگرافی نمایشی پوستر', 'فریم / برش / حرکت', 2),
('siosepol-art-book', 'سی‌وسه‌پل — کتاب هنری', 'Si-o-se-pol Art Book', 3, '۱۴۰۳', '2024', 'نشر مستقل، اصفهان', 'طراح کتاب و صفحه‌آرا',
 'کتابی که معماری پل را با گرید صفحه ترجمه می‌کند: طاق‌ها تبدیل به ستون‌های متن می‌شوند.',
 'ایدهٔ مرکزی ساده بود: گرید کتاب، پلان پل است. هر دهانه یک ستون متن، هر طاق یک صفحهٔ تصویر تمام‌عرض. ریتم ورق‌زدن، ریتم عبور از پل شد.\n\nدو کاغذ متفاوت — یک کاغذ بالکیِ گرم برای متن و یک کاغذ نیمه‌براق برای تصاویر — تجربهٔ لمسی دوگانه‌ای ساختند که روایت تاریخی و عکاسی معاصر را از هم جدا می‌کند.\n\nصحافی سوئیسی با دوخت نمایان انتخاب شد تا ستون فقرات کتاب، مثل ستون‌های پل، دیده شود.',
 '/assets/uploads/art-book.jpg', 'گرید دهانه‌ها', 'طاق، ستون، سایه', 3),
('hamnava-fintech-identity', 'هم‌نوا — هویت بصری فین‌تک', 'Hamnava Fintech', 1, '۱۴۰۴', '2025', 'استارتاپ هم‌نوا', 'طراح هویت و سیستم دیجیتال',
 'اعتماد مالی با زبان هندسی: یک نشان ماژولار که مثل نمودار رشد می‌کند.',
 'هم‌نوا محصولی برای مدیریت پس‌انداز خانوار است؛ پس هویت باید هم دقیق باشد هم انسانی. نشان برند از ماژول‌های مربعی ساخته شد که کنار هم موج می‌سازند — استعاره‌ای از هم‌نوا شدن پول‌های خرد.\n\nسیستم رنگ روی تضاد مرکب و آبی کبالتی سوار است؛ رنگی که در داشبورد محصول هم دقیقاً همان کد را دارد تا مرز برند و رابط محو شود.\n\nخروجی پروژه یک کیت کامل دیجیتال بود: کامپوننت‌های فیگما، قالب ارائه، کارت ویزیت و یک مینی‌سایت معرفی.',
 '/assets/uploads/fintech-system.jpg', 'نشان ماژولار', 'هم‌نوا / موج / رشد', 4),
('red-gold-saffron-packaging', 'طلای سرخ — بسته‌بندی زعفران', 'Red Gold Saffron', 4, '۱۴۰۲', '2023', 'هلدینگ صادراتی طلای سرخ', 'طراح بسته‌بندی',
 'سوغات ایرانی با قواعد سوئیسی: الگوی گره‌چینی که فقط با دو رنگ کار می‌کند.',
 'بسته‌بندی زعفران معمولاً در طلا و شلوغی غرق می‌شود. اینجا برعکس عمل کردیم: یک جعبهٔ مقواییِ سخت با روکش کاغذ مخملی، و الگویی هندسی برگرفته از گره‌چینی که تنها با سرخِ عمیق و طلاییِ مهارشده چاپ می‌شود.\n\nشیشهٔ داخلی کوچک و سنگین انتخاب شد تا محصول مثل یک نگین در محفظه بایستد؛ و نسخهٔ صادراتی با برچسب دوزبانهٔ فارسی–انگلیسی و کد رهگیری طراحی گردید.\n\nاین بسته‌بندی در نمایشگاه مواد غذایی تهران معرفی شد و برای سه بازار صادراتی بومی‌سازی شد.',
 '/assets/uploads/saffron-packaging.jpg', 'الگوی گره‌چینی', 'گره، نگین، طلا', 5),
('didar-business-cards', 'دیدار — ست کارت ویزیت لتربرس', 'Didar Letterpress Cards', 5, '۱۴۴', '2025', 'مشاورهٔ مدیریت دیدار', 'طراح و ناظر چاپ لتربرس',
 'یک کارت، سه لمس: کاغذ پنبه‌ای ضخیم، مونوگرام کور و لبهٔ رنگیِ آجری.',
 'برند دیدار روی گفت‌وگوی رو در رو بنا شده؛ پس کارت ویزیت باید همان حس حضور را بدهد. کاغذ ۶۰۰ گرمی پنبه‌ای با چاپ لتربرس انتخاب شد تا مونوگرام نه دیده شود، بلکه حس شود.\n\nلبهٔ رنگی آجری تنها عنصر رنگی ست است؛ امضایی که کارت را در دستهٔ کارت‌های معمولی غریبه می‌کند.\n\nچاپ در سه مرحله و با کلیشهٔ فلزی اختصاصی انجام شد و هر کارت شماره‌گذاری دستی شد.',
 '/assets/uploads/business-cards.jpg', 'مونوگرام لتربرس', 'دیدار / حضور / لمس', 6),
('book-festival-city-banner', 'جشنوارهٔ کتاب — بنر شهری', 'Tehran Book Festival Billboard', 6, '۱۴۰۳', '2024', 'شهرداری تهران، جشنوارهٔ کتاب', 'طراح بنر و آرت دیرکتور شهری',
 'یک بنر که در سه ثانیهٔ عبور از اتوبان خوانده می‌شود: گرید مورب، دو رنگ، بدون عکس.',
 'مسئله روشن بود: مخاطب بنر شهری فرصت ندارد. پس طراحی بر پایهٔ کنتراست فرم و رنگ بنا شد، نه تصویر و متن زیاد. گرید مورب، چشم را از گوشهٔ بالا به پیام اصلی می‌کشاند.\n\nپالت دو رنگ مرکب و آجری در مقیاس ده متری تست شد؛ رنگ‌هایی که در نور ظهر و مه شب هر دو دیده می‌مانند.\n\nنسخه‌های ۳در۶، ۱۲در۱۸ و استوری شبکه‌های اجتماعی از یک سیستم واحد مشتاق شدند.',
 '/assets/uploads/city-banner.jpg', 'گرید مورب شهری', 'دیدن / عبور / خواندن', 7),
('kashan-display-typeface', 'کاشان — تایپ‌فیس نمایشی فارسی', 'Kashan Display Typeface', 7, '۱۴۰۲', '2023', 'پروژهٔ شخصی', 'طراح حرف',
 'یک قلم نمایشی که لطافت نستعلیق را با ساختار هندسیِ مدرن آشتی می‌دهد.',
 'کاشان از یک پرسش شخصی شروع شد: چرا حروف نمایشی فارسی باید بین سنتیِ کامل و لاتینِ ترجمه‌شده یکی را انتخاب کنند؟ این قلم، منحنیِ قلم‌نی را نگه می‌دارد اما انتهاها را با برش هندسی می‌بندد.\n\nفرآیند طراحی با مرکب و قلم نی روی کاغذ آغاز شد و سپس دیجیتال گردید؛ هر اتصال با تست‌های خوانایی در اندازهٔ نمایشی بازبینی شد.\n\nاسپسیمن انتشار، یک پوستر تک‌رنگ است که یک حرف را در اندازهٔ یک‌ونیم متری نشان می‌دهد — همان‌طور که یک قلم نمایشی باید دیده شود.',
 '/assets/uploads/persian-typeface.jpg', 'اسپسیمن قلم کاشان', 'مشقِ شب', 8);

INSERT INTO project_services (project_id, label, sort) VALUES
(1, 'پژوهش و استراتژی بصری', 1), (1, 'طراحی نشان و سیستم لوگو', 2), (1, 'بسته‌بندی و لی‌اوت', 3), (1, 'راهنمای برند', 4),
(2, 'مفهوم‌پردازی', 1), (2, 'تایپوگرافی نمایشی', 2), (2, 'سیستم پوستر سه‌گانه', 3), (2, 'نظارت بر چاپ ریزوگراف', 4),
(3, 'گردآوری و ویرایش تصویر', 1), (3, 'گرید و صفحه‌آرایی', 2), (3, 'انتخاب کاغذ و صحافی', 3), (3, 'نظارت چاپ افست', 4),
(4, 'نام‌نگاری و نشان', 1), (4, 'سیستم رنگ و گرید', 2), (4, 'کیت رابط دیجیتال', 3), (4, 'قالب‌های ارائه', 4),
(5, 'ساختار بسته', 1), (5, 'الگوی هندسی ایرانی', 2), (5, 'طلاکوب و متریال', 3), (5, 'نسخه‌های صادراتی', 4),
(6, 'طراحی مونوگرام', 1), (6, 'انتخاب کاغذ و فینیشینگ', 2), (6, 'نظارت بر چاپ لتربرس', 3),
(7, 'سیستم بنر شهری', 1), (7, 'نسخه‌های چنداندازه', 2), (7, 'نسخهٔ شبکه‌های اجتماعی', 3),
(8, 'طراحی قلم نمایشی', 1), (8, 'اسپسیمن و پوستر', 2), (8, 'تست خوانایی', 3), (8, 'انتشار نسخهٔ وب', 4);

INSERT INTO project_palette (project_id, hex, name, sort) VALUES
(1, '#191712', 'مرکب', 1), (1, '#F4F1EA', 'کاغذ خام', 2), (1, '#E4471C', 'آجری', 3), (1, '#8A7B63', 'پوست گردو', 4),
(2, '#111111', 'مشکی فریم', 1), (2, '#EDE9E0', 'کاغذ کاهی', 2), (2, '#D81E1E', 'قرمز ریزو', 3),
(3, '#F2EEE4', 'بالکی گرم', 1), (3, '#1B1A17', 'مرکب متن', 2), (3, '#E8B019', 'زعفرانی', 3),
(4, '#101820', 'مرکب شب', 1), (4, '#F5F4F0', 'سفید رابط', 2), (4, '#1F4FD8', 'آبی کبالتی', 3),
(5, '#7A1220', 'سرخ عنابی', 1), (5, '#C9A227', 'طلایی مهارشده', 2), (5, '#F3EFE6', 'کرم مخملی', 3),
(6, '#191712', 'مرکب لتربرس', 1), (6, '#F6F3EC', 'پنبه‌ای', 2), (6, '#E4471C', 'لبهٔ آجری', 3),
(7, '#191712', 'مرکب', 1), (7, '#E4471C', 'آجری', 2), (7, '#EFEBE2', 'بتنی روشن', 3),
(8, '#141310', 'مرکب نی', 1), (8, '#F1EDE3', 'کاغذ کاهی', 2);

INSERT INTO project_metrics (project_id, value, label, sort) VALUES
(1, '۴۸', 'صفحه راهنمای برند', 1), (1, '۱۲', 'قلم بسته‌بندی', 2), (1, '۳', 'ماه اجرا', 3),
(2, '۳', 'پوستر اصلی', 1), (2, '۲۰۰', 'نسخهٔ محدود', 2), (2, '۲', 'رنگ ریزوگراف', 3),
(3, '۲۱۶', 'صفحه', 1), (3, '۳۳', 'دهانهٔ گرید', 2), (3, '۲', 'نوع کاغذ', 3),
(4, '۶۴', 'کامپوننت برند', 1), (4, '۲', 'پلتفرم دیجیتال', 2), (4, '۶', 'هفته اجرا', 3),
(5, '۳', 'بازار صادراتی', 1), (5, '۵', 'قالب بسته', 2), (5, '۱', 'الگوی انحصاری', 3),
(6, '۶۰۰', 'گرم کاغذ پنبه‌ای', 1), (6, '۳', 'مرحله چاپ', 2), (6, '۱', 'کلیشهٔ اختصاصی', 3),
(7, '۳', 'اندازهٔ بنر', 1), (7, '۲', 'رنگ', 2), (7, '۳', 'ثانیه دید', 3),
(8, '۳۸۰', 'گلیف', 1), (8, '۴', 'وزن', 2), (8, '۱', 'اسپسیمن چاپی', 3);

INSERT INTO services (title, title_en, description, sort) VALUES
('هویت بصری و برندینگ', 'Brand Identity', 'از استراتژی و نام‌نگاری تا نشان، سیستم رنگ و راهنمای برند؛ هویتی که در همهٔ تماس‌ها یک‌صدا می‌ماند.', 1),
('طراحی لوگو و نشان', 'Logo Design', 'لوگویی که در یک نگاه خوانده می‌شود و در هر اندازه‌ای — از مهر تا بیلبورد — کار می‌کند.', 2),
('پوستر و بنر', 'Poster & Billboard', 'پوستر فرهنگی و تبلیغاتی، بنر شهری و ست کمپین؛ پیامی که از دور دیده می‌شود و از نزدیک پاداش می‌دهد.', 3),
('کتاب و نشر', 'Print & Editorial', 'کتاب، نشریه، کاتالوگ و صفحه‌آرایی؛ با وسواس روی گرید، کاغذ و لحظهٔ چاپ.', 4),
('کارت ویزیت و ست اداری', 'Stationery', 'کارت ویزیت لتربرس، سربرگ، پاکت و ست اداری؛ جزئیاتی که هر روز لمس می‌شوند.', 5),
('بسته‌بندی', 'Packaging', 'ساختار، لی‌اوت و متریال؛ بسته‌ای که در قفسه ساکت نمی‌ماند اما فریاد هم نمی‌زند.', 6),
('تایپوگرافی و حروف‌چینی', 'Typography', 'طراحی حرف، انتخاب قلم و صفحه‌آرایی دوزبانهٔ فارسی و لاتین با سلسله‌مراتب درست.', 7);

INSERT INTO service_items (service_id, label, sort) VALUES
(1, 'پژوهش و موقعیت‌یابی', 1), (1, 'طراحی نشان و لوگوتایپ', 2), (1, 'سیستم رنگ و تایپوگرافی', 3), (1, 'راهنمای کاربرد برند', 4),
(2, 'اسکچ و مفهوم', 1), (2, 'رقومی‌سازی و تست اندازه', 2), (2, 'نسخه‌های رنگ و تک‌رنگ', 3), (2, 'فایل‌های استاندارد چاپ', 4),
(3, 'مفهوم‌پردازی کمپین', 1), (3, 'پوستر فرهنگی و تبلیغاتی', 2), (3, 'بنر شهری و بیلبورد', 3), (3, 'نسخهٔ شبکه‌های اجتماعی', 4),
(4, 'پوستر و مجموعه پوستر', 1), (4, 'کتاب و نشریه', 2), (4, 'کاتالوگ و گزارش سالانه', 3), (4, 'نظارت بر چاپ و صحافی', 4),
(5, 'کارت ویزیت لتربرس و افست', 1), (5, 'سربرگ و پاکت', 2), (5, 'مهر و اقلام اداری', 3), (5, 'فولدر و سررسید', 4),
(6, 'طراحی ساختار و دای‌لاین', 1), (6, 'لی‌اوت و اطلاعات', 2), (6, 'انتخاب متریال و فینیشینگ', 3), (6, 'نسخه‌های صادراتی', 4),
(7, 'طراحی تایپ‌فیس نمایشی', 1), (7, 'حروف‌چینی فارسی/لاتین', 2), (7, 'سیستم تایپوگرافی رابط', 3), (7, 'اسپسیمن و پوستر تایپ', 4);

INSERT INTO process_steps (title, description, sort) VALUES
('کشف و پژوهش', 'گفت‌وگو، مطالعهٔ بازار و رقبا، و تعریف مسئله‌ای که طراحی باید حل کند.', 1),
('مفهوم و اسکچ', 'ایده‌پردازی روی کاغذ، تست سریع مسیرها و انتخاب یک جهت قوی.', 2),
('طراحی و سیستم', 'ساخت سیستم بصری کامل: گرید، رنگ، تایپوگرافی و قواعد کاربرد.', 3),
('تحویل و نظارت', 'فایل‌های چاپ، راهنمای برند و حضور پای دستگاه چاپ تا تأیید نهایی.', 4);

INSERT INTO stats (value, label, sort) VALUES
('۱۲', 'سال تجربه', 1), ('۸۶', 'پروژهٔ تحویل‌شده', 2), ('۲۴', 'برند ساخته‌شده', 3), ('۹', 'جایزهٔ طراحی', 4);

INSERT INTO timeline (period, title, description, sort) VALUES
('۱۳۹۸ — اکنون', 'استودیوی مستقل میر خلیلی', 'مدیر هنری و طراح ارشد؛ همکاری با برندهای فرهنگی و مصرفی.', 1),
('۱۳۹۵ — ۱۳۹۸', 'مدیر هنری، استودیو نگاره', 'مسئول هویت بصری و سیستم‌های چاپی برای برندهای ملی.', 2),
('۱۳۹۲ — ۱۳۹۵', 'طراح گرافیک، آژانس تصویر', 'طراحی پوستر، بسته‌بندی و کمپین‌های فروشگاهی.', 3);

INSERT INTO awards (year_fa, title, project, sort) VALUES
('۱۴۰۴', 'نشان طلای جشنوارهٔ طراحی ایران', 'هویت بصری رُست', 1),
('۱۴۰۳', 'دیپلم افتخار بین‌ال گرافیک تهران', 'مجموعه پوستر جشنوارهٔ فیلم کوتاه', 2),
('۱۴۰۲', 'برندهٔ بخش بسته‌بندی، جشنوارهٔ چاپ', 'طلای سرخ', 3),
('۱۴۰۱', 'انتخاب سردبیر، سالنامهٔ تایپوگرافی فارسی', 'قلم کاشان', 4);

INSERT INTO skills (name, sort) VALUES
('Illustrator', 1), ('InDesign', 2), ('Photoshop', 3), ('Figma', 4), ('Glyphs', 5), ('After Effects', 6);
