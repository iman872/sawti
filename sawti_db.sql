

-- ============================================================
-- SAWTI.MA – Base de données / قاعدة البيانات
-- ============================================================

CREATE DATABASE IF NOT EXISTS sawti_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sawti_db;

-- -------------------------------------------------------
-- Table: departments (الأقسام)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name_fr VARCHAR(150) NOT NULL,
  name_ar VARCHAR(150) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO departments (name_fr, name_ar) VALUES
  ('Ministère de la Santé', 'وزارة الصحة'),
  ('Ministère de l\'Éducation', 'وزارة التربية الوطنية'),
  ('Ministère de l\'Intérieur', 'وزارة الداخلية'),
  ('Collectivités Locales', 'الجماعات الترابية'),
  ('Ministère des Finances', 'وزارة المالية'),
  ('Ministère de la Justice', 'وزارة العدل'),
  ('Ministère du Transport', 'وزارة النقل'),
  ('Autres', 'أخرى');

-- -------------------------------------------------------
-- Table: users (المستخدمون)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  phone VARCHAR(20),
  cin VARCHAR(20),
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  is_active TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin account (password: admin123)
-- Hash generated with PHP: password_hash('admin123', PASSWORD_BCRYPT)
INSERT INTO users (full_name, email, phone, cin, password, role) VALUES
  ('Administrateur', 'admin@sawti.ma', '0600000000', 'AA000000',
   '$2y$10$TKh8H1.PfQ5VknL86/C3ZuFJTLlLPU.oNBEqKPkd6hD4sO3p.K0W.', 'admin');

-- -------------------------------------------------------
-- Table: complaints (الشكايات)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS complaints (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  department_id INT,
  title VARCHAR(255) NOT NULL,
  request_type VARCHAR(50) NOT NULL,
  main_topic VARCHAR(150) NOT NULL,
  description TEXT NOT NULL,
  city VARCHAR(100),
  region_id INT DEFAULT NULL,
  province_id INT DEFAULT NULL,
  commune_id INT DEFAULT NULL,
  file_path VARCHAR(500),
  status ENUM('pending','processing','done','rejected') DEFAULT 'pending',
  tracking_code VARCHAR(20) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (department_id) REFERENCES departments(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Table: responses (الردود)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS responses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  complaint_id INT NOT NULL,
  admin_id INT NOT NULL,
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE,
  FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Table: notifications (الإشعارات)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS notifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  complaint_id INT,
  message_fr TEXT NOT NULL,
  message_ar TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -------------------------------------------------------
-- Table: regions (الجهات)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS regions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name_ar VARCHAR(150) NOT NULL,
  name_fr VARCHAR(150) NOT NULL
) ENGINE=InnoDB;

INSERT INTO regions (name_ar, name_fr) VALUES
  ('طنجة-تطوان-الحسيمة','Tanger-Tétouan-Al Hoceïma'),
  ('الشرق','L\'Oriental'),
  ('فاس-مكناس','Fès-Meknès'),
  ('الرباط-سلا-القنيطرة','Rabat-Salé-Kénitra'),
  ('بني ملال-خنيفرة','Béni Mellal-Khénifra'),
  ('الدار البيضاء-سطات','Casablanca-Settat'),
  ('مراكش-آسفي','Marrakech-Safi'),
  ('درعة-تافيلالت','Drâa-Tafilalet'),
  ('سوس-ماسة','Souss-Massa'),
  ('كلميم-واد نون','Guelmim-Oued Noun'),
  ('العيون-الساقية الحمراء','Laâyoune-Sakia El Hamra'),
  ('الداخلة-وادي الذهب','Dakhla-Oued Ed-Dahab');

-- -------------------------------------------------------
-- Table: provinces (الأقاليم والعمالات)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS provinces (
  id INT AUTO_INCREMENT PRIMARY KEY,
  region_id INT NOT NULL,
  name_ar VARCHAR(150) NOT NULL,
  name_fr VARCHAR(150) NOT NULL,
  FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO provinces (region_id, name_ar, name_fr) VALUES
  -- طنجة-تطوان-الحسيمة (1)
  (1,'عمالة طنجة-أصيلة','Préfecture de Tanger-Assilah'),
  (1,'إقليم المضيق-الفنيدق','Province M\'diq-Fnideq'),
  (1,'إقليم تطوان','Province de Tétouan'),
  (1,'إقليم الفحص-أنجرة','Province Fahs-Anjra'),
  (1,'إقليم العرائش','Province de Larache'),
  (1,'إقليم الحسيمة','Province d\'Al Hoceïma'),
  (1,'إقليم شفشاون','Province de Chefchaouen'),
  (1,'إقليم وزان','Province de Ouezzane'),
  -- الشرق (2)
  (2,'عمالة وجدة-أنكاد','Préfecture Oujda-Angad'),
  (2,'إقليم بركان','Province de Berkane'),
  (2,'إقليم تاوريرت','Province de Taourirt'),
  (2,'إقليم جرادة','Province de Jerada'),
  (2,'إقليم الناظور','Province de Nador'),
  (2,'إقليم الدريوش','Province de Driouch'),
  (2,'إقليم فجيج','Province de Figuig'),
  (2,'إقليم كرسيف','Province de Guercif'),
  -- فاس-مكناس (3)
  (3,'عمالة فاس','Préfecture de Fès'),
  (3,'عمالة مكناس','Préfecture de Meknès'),
  (3,'إقليم إفران','Province d\'Ifrane'),
  (3,'إقليم بولمان','Province de Boulemane'),
  (3,'إقليم الحاجب','Province d\'El Hajeb'),
  (3,'إقليم صفرو','Province de Sefrou'),
  (3,'إقليم مولاي يعقوب','Province de Moulay Yaâcoub'),
  (3,'إقليم تاونات','Province de Taounate'),
  (3,'إقليم تازة','Province de Taza'),
  -- الرباط-سلا-القنيطرة (4)
  (4,'عمالة الرباط','Préfecture de Rabat'),
  (4,'عمالة سلا','Préfecture de Salé'),
  (4,'عمالة الصخيرات-تمارة','Préfecture de Skhirate-Témara'),
  (4,'إقليم القنيطرة','Province de Kénitra'),
  (4,'إقليم الخميسات','Province de Khémisset'),
  (4,'إقليم القصر الكبير','Province de Ksar El Kébir'),
  (4,'إقليم سيدي قاسم','Province de Sidi Kacem'),
  (4,'إقليم سيدي سليمان','Province de Sidi Slimane'),
  -- بني ملال-خنيفرة (5)
  (5,'إقليم بني ملال','Province de Béni Mellal'),
  (5,'إقليم أزيلال','Province d\'Azilal'),
  (5,'إقليم الفقيه بن صالح','Province de Fkih Ben Salah'),
  (5,'إقليم خنيفرة','Province de Khénifra'),
  (5,'إقليم خريبكة','Province de Khouribga'),
  -- الدار البيضاء-سطات (6)
  (6,'عمالة الدار البيضاء','Préfecture de Casablanca'),
  (6,'عمالة المحمدية','Préfecture de Mohammadia'),
  (6,'إقليم سطات','Province de Settat'),
  (6,'إقليم بن سليمان','Province de Ben Slimane'),
  (6,'إقليم برشيد','Province de Berrechid'),
  (6,'إقليم الجديدة','Province d\'El Jadida'),
  (6,'إقليم سيدي بنور','Province de Sidi Bennour'),
  (6,'إقليم النواصر','Province de Nouaceur'),
  (6,'إقليم مديونة','Province de Mediouna'),
  -- مراكش-آسفي (7)
  (7,'عمالة مراكش','Préfecture de Marrakech'),
  (7,'إقليم الحوز','Province d\'Al Haouz'),
  (7,'إقليم قلعة السراغنة','Province d\'El Kelâa des Sraghna'),
  (7,'إقليم الرحامنة','Province de Rhamna'),
  (7,'إقليم شيشاوة','Province de Chichaoua'),
  (7,'إقليم آسفي','Province de Safi'),
  (7,'إقليم الصويرة','Province d\'Essaouira'),
  -- درعة-تافيلالت (8)
  (8,'إقليم ورزازات','Province de Ouarzazate'),
  (8,'إقليم ميدلت','Province de Midelt'),
  (8,'إقليم الرشيدية','Province d\'Errachidia'),
  (8,'إقليم زاكورة','Province de Zagora'),
  (8,'إقليم تنغير','Province de Tinghir'),
  -- سوس-ماسة (9)
  (9,'عمالة أكادير إداوتنان','Préfecture d\'Agadir Ida-Outanane'),
  (9,'إقليم إنزكان-أيت ملول','Province d\'Inezgane-Aït Melloul'),
  (9,'إقليم تارودانت','Province de Taroudant'),
  (9,'إقليم تيزنيت','Province de Tiznit'),
  (9,'إقليم شتوكة-آيت باها','Province de Chtouka-Aït Baha'),
  (9,'إقليم طاطا','Province de Tata'),
  -- كلميم-واد نون (10)
  (10,'إقليم كلميم','Province de Guelmim'),
  (10,'إقليم تان-تان','Province de Tan-Tan'),
  (10,'إقليم سيدي إفني','Province de Sidi Ifni'),
  (10,'إقليم آسا-الزاك','Province d\'Assa-Zag'),
  -- العيون-الساقية الحمراء (11)
  (11,'إقليم العيون','Province de Laâyoune'),
  (11,'إقليم بوجدور','Province de Boujdour'),
  (11,'إقليم طرفاية','Province de Tarfaya'),
  (11,'إقليم السمارة','Province de Smara'),
  -- الداخلة-وادي الذهب (12)
  (12,'إقليم الداخلة-وادي الذهب','Province de Dakhla-Oued Ed-Dahab'),
  (12,'إقليم أوسرد','Province d\'Aousserd');

-- -------------------------------------------------------
-- Table: communes (الجماعات)
-- -------------------------------------------------------
CREATE TABLE IF NOT EXISTS communes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  province_id INT NOT NULL,
  name_ar VARCHAR(150) NOT NULL,
  name_fr VARCHAR(150) NOT NULL,
  FOREIGN KEY (province_id) REFERENCES provinces(id) ON DELETE CASCADE
) ENGINE=InnoDB;

INSERT INTO communes (province_id, name_ar, name_fr) VALUES
  -- طنجة-أصيلة (1)
  (1,'طنجة المدينة','Tanger Médina'),
  (1,'المرشان','Marchane'),
  (1,'الشرف','Charf'),
  (1,'أصيلة','Assilah'),
  -- تطوان (3)
  (3,'تطوان','Tétouan'),
  (3,'مرتيل','Martil'),
  (3,'تمودة','Timouda'),
  -- العرائش (5)
  (5,'العرائش','Larache'),
  (5,'القصر الكبير','Ksar El Kébir'),
  (5,'العوامرة','Ouamra'),
  -- الحسيمة (6)
  (6,'الحسيمة','Al Hoceïma'),
  (6,'بني بوعياش','Bni Bouayach'),
  (6,'إمزورن','Imzouren'),
  -- وجدة (9)
  (9,'وجدة','Oujda'),
  (9,'سيدي زيان','Sidi Ziane'),
  (9,'عين الصفا','Aïn Sfa'),
  -- بركان (10)
  (10,'بركان','Berkane'),
  (10,'سعيدية','Saïdia'),
  (10,'أحفير','Ahfir'),
  -- الناظور (13)
  (13,'الناظور','Nador'),
  (13,'الزيو','Zio'),
  (13,'بني أنصار','Bni Ansar'),
  -- فاس (17)
  (17,'فاس المدينة','Fès Médina'),
  (17,'زواغة-مولاي رشيد','Zouagha-Moulay Rachid'),
  (17,'مرينة','Mérinides'),
  (17,'سيدي هرازم','Sidi Harazem'),
  -- مكناس (18)
  (18,'مكناس','Meknès'),
  (18,'حمرية','Hamriya'),
  (18,'زرهون','Zerhoun'),
  -- الرباط (25)
  (25,'الرباط','Rabat'),
  (25,'أكدال-الرياض','Agdal-Ryad'),
  (25,'حسان','Hassan'),
  (25,'سويسي','Souissi'),
  -- سلا (26)
  (26,'سلا','Salé'),
  (26,'سلا الجديدة','Sala Al Jadida'),
  (26,'العوينة','Ouinane'),
  -- تمارة (27)
  (27,'تمارة','Témara'),
  (27,'الصخيرات','Skhirate'),
  (27,'عين عاطيق','Aïn Atiq'),
  -- القنيطرة (28)
  (28,'القنيطرة','Kénitra'),
  (28,'مهدية','Mehdia'),
  (28,'سيدي طيبي','Sidi Taibi'),
  -- الدار البيضاء (41)
  (41,'الدار البيضاء','Casablanca'),
  (41,'عين السبع','Aïn Sebaa'),
  (41,'أنفا','Anfa'),
  (41,'المعاريف','Maârif'),
  (41,'سيدي عثمان','Sidi Othmane'),
  (41,'الفداء','Hay Hassani'),
  -- مراكش (50)
  (50,'مراكش','Marrakech'),
  (50,'منارة','Menara'),
  (50,'سيدي يوسف بن علي','Sidi Youssef Ben Ali'),
  (50,'المسيرة','Al Massira'),
  -- أكادير (59)
  (59,'أكادير','Agadir'),
  (59,'بنسركاو','Bensergao'),
  (59,'تيكيوين','Tikiouine'),
  -- تارودانت (61)
  (61,'تارودانت','Taroudant'),
  (61,'أولوز','Oulad Teïma'),
  (61,'تيوت','Tiout'),
  -- العيون (70)
  (70,'العيون','Laâyoune'),
  (70,'دشيرة الجهادية','Dcheira El Jihadia'),
  -- الداخلة (74)
  (74,'الداخلة','Dakhla'),
  (74,'الطرفاية','Bir Gandouz');