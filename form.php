<?php
session_start();
require_once __DIR__ . '/config.php';

if (!isset($pdo)) {
    die("❌ PDO connection was not initialized. Check config.php.");
}

$orgId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($orgId <= 0) {
    die("⚠️ لم يتم تحديد رقم الإدارة. الرجاء فتح الصفحة بهذا الشكل: <a href='form.php?id=1'>form.php?id=1</a> ثم اختر الإدارة المطلوبة.");
}

$stmt = $pdo->prepare("SELECT * FROM departments WHERE id = ?");
$stmt->execute([$orgId]);
$organization = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$organization) {
    die("⚠️ لم يتم العثور على الإدارة المطلوبة. <a href='index.html'>العودة للرئيسية</a>");
}

$regions = $pdo->query("SELECT * FROM regions ORDER BY name_ar")->fetchAll();
$captchaCode = rand(1000, 9999);
$_SESSION['captcha_code'] = $captchaCode;
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقديم شكاية إلى <?php echo htmlspecialchars($organization['name_ar']); ?> - منصة صوتي</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Tajawal', sans-serif;
            background: radial-gradient(circle at top left, rgba(0, 98, 51, 0.12), transparent 25%), radial-gradient(circle at bottom right, rgba(193, 39, 45, 0.14), transparent 30%), #f4f7fb;
            color: #1f2937;
            min-height: 100vh;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.62), rgba(255, 255, 255, 0.3));
            pointer-events: none;
        }

        .container {
            width: min(1180px, calc(100% - 30px));
            margin: 40px auto 60px;
        }

        .glass-card {
            border-radius: 32px;
            background: rgba(255, 255, 255, 0.82);
            border: 1px solid rgba(255, 255, 255, 0.8);
            box-shadow: 0 28px 80px rgba(15, 23, 42, 0.12);
            backdrop-filter: blur(22px);
            overflow: hidden;
        }

        .card-header {
            padding: 34px 38px;
            background: linear-gradient(135deg, #006233 0%, #C1272D 100%);
            color: #fff;
        }

        .org-name {
            display: flex;
            gap: 14px;
            align-items: center;
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 10px;
        }

        .org-name i {
            font-size: 1.6rem;
        }

        .card-header p {
            opacity: .92;
            line-height: 1.7;
        }

        .alert-warning-custom {
            margin: 0 38px 20px;
            padding: 22px 26px;
            background: rgba(248, 250, 252, 0.96);
            border-right: 4px solid #C1272D;
            border-radius: 22px;
            color: #334155;
        }

        .alert-title {
            font-weight: 800;
            margin-bottom: 10px;
            color: #C1272D;
        }

        .alert-warning-custom ul {
            list-style: disc;
            padding-inline-start: 20px;
            margin-top: 12px;
        }

        .progress-indicator {
            margin: 0 38px 20px;
            background: #e2e8f0;
            border-radius: 999px;
            height: 10px;
            overflow: hidden;
        }

        .progress-fill {
            width: 0%;
            height: 100%;
            background: linear-gradient(90deg, #006233, #C1272D);
            transition: width .3s ease;
        }

        .progress-text {
            margin: 12px 38px;
            text-align: left;
            color: #475569;
            font-weight: 600;
        }

        form {
            padding: 0 38px 38px;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 24px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }

        label {
            font-weight: 700;
            color: #0f172a;
        }

        .required::after {
            content: ' *';
            color: #C1272D;
        }

        input,
        select,
        textarea {
            width: 100%;
            border-radius: 18px;
            border: 1px solid #cbd5e1;
            background: rgba(255, 255, 255, 0.92);
            padding: 14px 16px;
            font-size: 1rem;
            color: #0f172a;
            transition: border .25s ease, box-shadow .25s ease;
        }

        input:focus,
        select:focus,
        textarea:focus {
            outline: none;
            border-color: #006233;
            box-shadow: 0 0 0 4px rgba(0, 98, 51, 0.1);
        }

        select:disabled {
            background: #f8fafc;
            color: #64748b;
        }

        textarea {
            min-height: 135px;
            resize: vertical;
        }

        .file-zone {
            position: relative;
            border: 2px dashed rgba(15, 23, 42, 0.16);
            border-radius: 24px;
            padding: 32px 24px;
            background: rgba(249, 250, 251, 0.9);
            text-align: center;
            cursor: pointer;
            transition: all .25s ease;
        }

        .file-zone:hover,
        .file-zone.dragover {
            border-color: #006233;
            background: rgba(0, 98, 51, 0.05);
        }

        .file-zone i {
            color: #006233;
            margin-bottom: 14px;
        }

        .file-zone p {
            font-size: 1rem;
            color: #334155;
            margin-bottom: 4px;
        }

        .file-zone small {
            color: #64748b;
        }

        .file-list {
            margin-top: 18px;
            list-style: none;
            display: grid;
            gap: 10px;
        }

        .file-list li {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            border-radius: 16px;
            background: #f8fafc;
            color: #0f172a;
            box-shadow: inset 0 1px 2px rgba(15, 23, 42, 0.06);
        }

        .captcha-box {
            display: grid;
            grid-template-columns: 160px 1fr;
            gap: 16px;
            align-items: center;
            background: #f8fafc;
            border-radius: 18px;
            padding: 18px;
            border: 1px solid #e2e8f0;
        }

        .captcha-number {
            display: flex;
            justify-content: center;
            align-items: center;
            background: #0f172a;
            color: #fff;
            border-radius: 16px;
            font-size: 1.6rem;
            letter-spacing: 8px;
            font-weight: 800;
            min-height: 72px;
        }

        .terms-card {
            display: flex;
            flex-direction: column;
            gap: 18px;
            padding: 24px 24px 18px;
            background: rgba(249, 250, 251, 0.95);
            border: 1px solid #e2e8f0;
            border-radius: 22px;
            margin-bottom: 24px;
        }

        .terms-card h3 {
            font-size: 1.05rem;
            color: #0f172a;
        }

        .terms-card p {
            color: #475569;
            line-height: 1.8;
            font-size: .97rem;
        }

        .terms {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 0;
        }

        .terms input {
            margin-top: 4px;
            width: auto;
            accent-color: #006233;
        }

        .terms label {
            color: #334155;
            font-weight: 600;
            line-height: 1.5;
        }

        .terms a {
            color: #006233;
            text-decoration: none;
        }

        .buttons-group {
            display: flex;
            gap: 18px;
            justify-content: center;
            flex-wrap: wrap;
            margin-top: 10px;
        }

        .btn {
            border: none;
            border-radius: 999px;
            padding: 16px 32px;
            font-size: 1rem;
            font-weight: 800;
            color: #fff;
            cursor: pointer;
            transition: transform .2s ease, box-shadow .2s ease;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 34px rgba(0, 0, 0, 0.12);
        }

        .btn-draft {
            background: #C1272D;
        }

        .btn-submit {
            background: #006233;
        }

        @media(max-width: 900px) {
            .form-grid {
                grid-template-columns: 1fr;
            }

            .captcha-box {
                grid-template-columns: 1fr;
            }
        }

        @media(max-width: 650px) {
            .container {
                margin: 20px auto 30px;
            }

            .card-header {
                padding: 26px 22px;
            }

            form {
                padding: 0 24px 28px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="glass-card">
            <div class="card-header">
                <div class="org-name"><i class="fas fa-building"></i>
                    <?php echo htmlspecialchars($organization['name_ar']); ?></div>
                <p>منصة صوتي.ma تمنحك واجهة متميزة لتقديم طلبك أو شكايتك مباشرة إلى الإدارة المعنية.</p>
            </div>

            <div class="alert-warning-custom">
                <div class="alert-title"><i class="fas fa-exclamation-triangle"></i> تنبيه مهم</div>
                <p>نرجو التأكد من دقة المعلومات قبل الإرسال. لا يتم قبول الشكايات أو الطلبات التي تحتوي على:</p>
                <ul>
                    <li>قضايا خاضعة للجوء إلى القضاء أو صدرت بشأنها أحكام نهائية.</li>
                    <li>معلومات غير صحيحة تؤثر على سير الاجراءات.</li>
                    <li>سب أو تشهير ضد أشخاص أو هيئات.</li>
                </ul>
            </div>

            <div class="progress-indicator">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="progress-text" id="progressText">0% مكتمل</div>

            <form id="complaintForm" action="process_submission.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="department_id" value="<?php echo $orgId; ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label class="required" for="title">عنوان الطلب</label>
                        <input id="title" type="text" name="title" placeholder="اكتب عنوانًا موجزًا لطلبك" required>
                    </div>
                    <div class="form-group">
                        <label class="required" for="requestType">نوع الطلب</label>
                        <select id="requestType" name="request_type" required>
                            <option value="">اختر النوع</option>
                            <option value="complaint">شكاية</option>
                            <option value="demand">طلب</option>
                            <option value="suggestion">اقتراح</option>
                            <option value="note">ملاحظة</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="required" for="mainTopic">الموضوع الرئيسي</label>
                        <select id="mainTopic" name="main_topic" required>
                            <option value="">اختر الموضوع</option>
                            <option value="الاستثمار">الاستثمار</option>
                            <option value="النزاهة ومحاربة الفساد">النزاهة ومحاربة الفساد</option>
                            <option value="تنفيذ الأحكام ضد الدولة">تنفيذ الأحكام ضد الدولة</option>
                            <option value="جودة الإدارات العمومية">جودة الإدارات العمومية</option>
                            <option value="قضايا عقارية">قضايا عقارية</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="required" for="regionSelect">الجهة</label>
                        <select id="regionSelect" name="region_id" required>
                            <option value="">اختر الجهة</option>
                            <?php foreach ($regions as $region): ?>
                                <option value="<?php echo $region['id']; ?>">
                                    <?php echo htmlspecialchars($region['name_ar']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="required" for="provinceSelect">الإقليم / العمالة</label>
                        <select id="provinceSelect" name="province_id" disabled required>
                            <option value="">اختر الإقليم أولاً</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="required" for="communeSelect">الجماعة</label>
                        <select id="communeSelect" name="commune_id" disabled required>
                            <option value="">اختر الجماعة أولاً</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="address">العنوان التفصيلي</label>
                    <input id="address" type="text" name="address"
                        placeholder="مثال: شارع محمد الخامس، رقم 12، حي الرياض">
                </div>

                <div class="form-group">
                    <label class="required" for="description">الوصف الكامل</label>
                    <textarea id="description" name="description" rows="6" placeholder="اكتب هنا تفاصيل طلبك بدقة..."
                        required></textarea>
                </div>

                <div class="form-group">
                    <label>المرفقات والوثائق المدعمة</label>
                    <div class="file-zone" id="fileDropZone">
                        <i class="fas fa-cloud-upload-alt fa-3x" style="color: #006233; margin-bottom: 15px;"></i>
                        <p style="font-weight: 700; font-size: 1.1rem; margin-bottom: 8px;">اسحب الملفات هنا أو انقر
                            للاختيار</p>
                        <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 12px;">يمكنك إرفاق صور، وثائق PDF،
                            أو تسجيلات صوتية لتعزيز طلبك</p>
                        <small
                            style="display: block; background: #f1f5f9; padding: 6px 12px; border-radius: 8px; color: #475569;">
                            PDF / JPG / PNG / ZIP / MP3 / WAV / MP4 - الحد الأقصى: 5MB لكل ملف
                        </small>
                        <input type="file" id="fileInput" name="attachments[]" multiple
                            accept="application/pdf,image/*,audio/*,video/*,.zip" hidden>
                    </div>
                    <ul class="file-list" id="fileList"></ul>
                </div>

                <div class="form-group">
                    <label class="required">التحقق</label>
                    <div class="captcha-box">
                        <div class="captcha-number"><?php echo $captchaCode; ?></div>
                        <input type="text" name="captcha_input" placeholder="أدخل الرقم الظاهر" required>
                    </div>
                </div>

                <div class="terms-card">
                    <h3>شروط الاستعمال</h3>
                    <p>أقر بأن جميع المعلومات الواردة في هذا الطلب صحيحة ودقيقة، وأن أي وثائق مرفقة تُقدم بغرض دعم الطلب
                        فقط. أتعهد بعدم تقديم بيانات مضللة أو محرفّة، وأوافق على أن تكون هذه الوثائق خاضعة للقوانين
                        المعمول بها، وأن الإدارة قد ترفض الطلب إذا تبين عدم اكتمال الشروط أو عدم مصداقية المعلومات.</p>
                    <div class="terms">
                        <input type="checkbox" id="termsCheckbox" name="terms" required>
                        <label for="termsCheckbox">أوافق على شروط الاستخدام وأقر بصحة المعلومات الواردة.</label>
                    </div>
                </div>

                <div class="buttons-group">
                    <button type="submit" name="action" value="draft" class="btn btn-draft"><i class="fas fa-save"></i>
                        حفظ كمسودة</button>
                    <button type="submit" name="action" value="submit" class="btn btn-submit"><i
                            class="fas fa-paper-plane"></i> إرسال الطلب</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const form = document.getElementById('complaintForm');
        const progressFill = document.getElementById('progressFill');
        const progressText = document.getElementById('progressText');
        const inputs = Array.from(form.querySelectorAll('input[required], select[required], textarea[required]'));

        function updateProgress() {
            const total = inputs.length;
            const completed = inputs.filter(input => input.value.trim() !== '').length;
            const percent = total ? Math.round((completed / total) * 100) : 0;
            progressFill.style.width = percent + '%';
            progressText.textContent = percent + '% مكتمل';
        }

        inputs.forEach(input => input.addEventListener('input', updateProgress));
        updateProgress();

        const dropZone = document.getElementById('fileDropZone');
        const fileInput = document.getElementById('fileInput');
        const fileList = document.getElementById('fileList');
        let droppedFiles = [];

        dropZone.addEventListener('click', () => fileInput.click());
        dropZone.addEventListener('dragenter', () => dropZone.classList.add('dragover'));
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('dragover'));
        dropZone.addEventListener('dragover', event => {
            event.preventDefault();
            dropZone.classList.add('dragover');
        });
        dropZone.addEventListener('drop', event => {
            event.preventDefault();
            dropZone.classList.remove('dragover');
            addFiles(Array.from(event.dataTransfer.files));
        });

        fileInput.addEventListener('change', event => addFiles(Array.from(event.target.files)));

        function addFiles(files) {
            const validFiles = [];
            files.forEach(file => {
                if (file.size > 5 * 1024 * 1024) {
                    alert(`الملف "${file.name}" أكبر من 5MB ولن يتم رفعه.`);
                    return;
                }

                if (!droppedFiles.some(existing => existing.name === file.name && existing.size === file.size)) {
                    droppedFiles.push(file);
                    validFiles.push(file);
                }
            });

            if (validFiles.length === 0) {
                return;
            }

            const dataTransfer = new DataTransfer();
            droppedFiles.forEach(file => dataTransfer.items.add(file));
            fileInput.files = dataTransfer.files;
            renderFileList();
        }

        function renderFileList() {
            fileList.innerHTML = '';
            droppedFiles.forEach(file => {
                const item = document.createElement('li');
                item.innerHTML = `<i class="fas fa-file-lines"></i> ${file.name}`;
                fileList.appendChild(item);
            });
        }

        const regionSelect = document.getElementById('regionSelect');
        const provinceSelect = document.getElementById('provinceSelect');
        const communeSelect = document.getElementById('communeSelect');

        regionSelect.addEventListener('change', () => {
            const regionId = regionSelect.value;
            provinceSelect.innerHTML = '<option value="">جاري التحميل...</option>';
            provinceSelect.disabled = true;
            communeSelect.innerHTML = '<option value="">اختر الجماعة أولاً</option>';
            communeSelect.disabled = true;

            if (!regionId) {
                provinceSelect.innerHTML = '<option value="">اختر الإقليم أولاً</option>';
                return;
            }

            fetch(`ajax/get_provinces.php?region_id=${encodeURIComponent(regionId)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        provinceSelect.innerHTML = '<option value="">اختر الإقليم / العمالة</option>';
                        data.forEach(item => {
                            provinceSelect.insertAdjacentHTML('beforeend', `<option value="${item.id}">${item.name_ar}</option>`);
                        });
                        provinceSelect.disabled = false;
                    } else {
                        provinceSelect.innerHTML = '<option value="">لا توجد أقاليم متوفرة حالياً</option>';
                        provinceSelect.disabled = true;
                    }
                })
                .catch(() => {
                    provinceSelect.innerHTML = '<option value="">فشلت عملية التحميل</option>';
                });
        });

        provinceSelect.addEventListener('change', () => {
            const provinceId = provinceSelect.value;
            communeSelect.innerHTML = '<option value="">جاري التحميل...</option>';
            communeSelect.disabled = true;

            if (!provinceId) {
                communeSelect.innerHTML = '<option value="">اختر الجماعة أولاً</option>';
                return;
            }

            fetch(`ajax/get_communes.php?province_id=${encodeURIComponent(provinceId)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.length > 0) {
                        communeSelect.innerHTML = '<option value="">اختر الجماعة</option>';
                        data.forEach(item => {
                            communeSelect.insertAdjacentHTML('beforeend', `<option value="${item.id}">${item.name_ar}</option>`);
                        });
                        communeSelect.disabled = false;
                    } else {
                        communeSelect.innerHTML = '<option value="">لا توجد جماعات مسجلة لهذا الإقليم</option>';
                        communeSelect.disabled = true;
                    }
                })
                .catch(() => {
                    communeSelect.innerHTML = '<option value="">فشلت عملية التحميل</option>';
                });
        });
    </script>
</body>

</html>