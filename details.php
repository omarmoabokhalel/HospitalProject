<?php
// إعداد الاتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "healthtech";

// إنشاء اتصال
$conn = new mysqli($servername, $username, $password, $dbname);

// التحقق من الاتصال
if ($conn->connect_error) {
    die("فشل الاتصال بقاعدة البيانات: " . $conn->connect_error);
}

// التحقق من نوع الطلب
if (isset($_POST['type'])) {
    $type = $_POST['type'];

    // جلب المحافظات
    if ($type == 'governorates') {
        $query = "SELECT DISTINCT governorate FROM hospitals ORDER BY governorate";
        $result = $conn->query($query);

        echo '<option value="">-- اختر المحافظة --</option>';
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['governorate'] . '">' . $row['governorate'] . '</option>';
            }
        }
    }

    // جلب المراكز بناءً على المحافظة المختارة
    if ($type == 'centers' && isset($_POST['governorate'])) {
        $governorate = $_POST['governorate'];
        $query = "SELECT DISTINCT center FROM hospitals WHERE governorate = ? ORDER BY center";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $governorate);
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<option value="">-- اختر المركز --</option>';
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['center'] . '">' . $row['center'] . '</option>';
            }
        }
    }

    // جلب المستشفيات بناءً على المركز المختار
    if ($type == 'hospitals' && isset($_POST['center'])) {
        $center = $_POST['center'];
        $query = "SELECT hospital_name FROM hospitals WHERE center = ? ORDER BY hospital_name";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $center);
        $stmt->execute();
        $result = $stmt->get_result();

        echo '<option value="">-- اختر المستشفى --</option>';
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['hospital_name'] . '">' . $row['hospital_name'] . '</option>';
            }
        }
    }

    // عرض تفاصيل المستشفى بناءً على اسم المستشفى المختار
    if ($type == 'hospitalDetails' && isset($_POST['hospital'])) {
        $hospital = $_POST['hospital'];
        $query = "SELECT * FROM hospitals WHERE hospital_name = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $hospital);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $hospitalDetails = $result->fetch_assoc();

            echo '<table>';
            echo '<tr><td>' . $hospitalDetails['hospital_name'] . '</td><th>اسم المستشفى</th></tr>';
            echo '<tr><td>' . $hospitalDetails['address'] . '</td><th>العنوان</th></tr>';
            echo '<tr><td>' . $hospitalDetails['phone'] . '</td><th>رقم الهاتف</th></tr>';
            echo '<tr><td>' . $hospitalDetails['email'] . '</td><th>البريد الإلكتروني</th></tr>';
            echo '<tr><td>' . $hospitalDetails['specialties'] . '</td><th>التخصصات المتاحة</th></tr>';
            echo '</table>';
        } else {
            echo "لا توجد تفاصيل متاحة للمستشفى المختار.";
        }
    }
}

// إغلاق الاتصال بقاعدة البيانات
$conn->close();
?>
