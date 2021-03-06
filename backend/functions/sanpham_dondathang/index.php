<?php
// hàm `session_id()` sẽ trả về giá trị SESSION_ID (tên file session do Web Server tự động tạo)
// - Nếu trả về Rỗng hoặc NULL => chưa có file Session tồn tại
if (session_id() === '') {
  // Yêu cầu Web Server tạo file Session để lưu trữ giá trị tương ứng với CLIENT (Web Browser đang gởi Request)
  session_start();
}
?>
<!-- Nhúng file cấu hình để xác định được Tên và Tiêu đề của trang hiện tại người dùng đang truy cập -->
<?php include_once(__DIR__ . '/../../layouts/config.php'); ?>



<?php 
  include_once(__DIR__. '/../../../dbconnect.php');
  if(isset($_SESSION['kh_tendangnhap_logged']))
    $kh_tendangnhap = $_SESSION['kh_tendangnhap_logged'];
    else $kh_tendangnhap = '';
  $sql_kh_tendangnhap = <<<EOT
  SELECT *
  FROM khachhang kh
  WHERE kh.kh_tendangnhap = '$kh_tendangnhap';
EOT;
  $result_kh_tendangnhap = mysqli_query($conn, $sql_kh_tendangnhap);
  while ($row_kh_tendangnhap = mysqli_fetch_array($result_kh_tendangnhap, MYSQLI_ASSOC)) {
    $kh_quantri = $row_kh_tendangnhap['kh_quantri'];
  }
  // var_dump($kh_quantri); die;
  if($kh_quantri==1) :
?>
<!DOCTYPE html>
<html>

<head>
  <!-- Nhúng file quản lý phần HEAD -->
  <?php include_once(__DIR__ . '/../../layouts/head.php'); ?>
  <!-- DataTable CSS -->
  <link href="/king/assets/vendor/DataTables/datatables.css" type="text/css" rel="stylesheet" />
  <link href="/king/assets/vendor/DataTables/Buttons-1.6.5/css/buttons.bootstrap4.min.css" type="text/css" rel="stylesheet" />

</head>

<body class="d-flex flex-column h-100">
  <!-- header -->
  <?php include_once(__DIR__ . '/../../layouts/partials/header.php'); ?>
  <!-- end header -->

  <div class="container-fluid">
    <div class="row">
      <!-- sidebar -->
      <?php include_once(__DIR__ . '/../../layouts/partials/sidebar.php'); ?>
      <!-- end sidebar -->

      <main role="main" class="col-md-10 ml-sm-auto px-4 mb-2">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
          <h1 class="h2">Danh sách Chi tiết đơn đặt hàng</h1>
        </div>

        <!-- Block content -->
        <?php
        // Truy vấn database để lấy danh sách
        // 1. Include file cấu hình kết nối đến database, khởi tạo kết nối $conn
        include_once(__DIR__. '/../../../dbconnect.php');

        // 2. Chuẩn bị câu truy vấn $sql
        $stt=1;
        $sql = "SELECT *	FROM sanpham_dondathang ORDER BY dh_ma;";

        // 3. Thực thi câu truy vấn SQL để lấy về dữ liệu
        $result = mysqli_query($conn, $sql);
        // 4. Khi thực thi các truy vấn dạng SELECT, dữ liệu lấy về cần phải phân tích để sử dụng
        // Thông thường, chúng ta sẽ sử dụng vòng lặp while để duyệt danh sách các dòng dữ liệu được SELECT
        // Ta sẽ tạo 1 mảng array để chứa các dữ liệu được trả về
        $ds_sanpham_dondathang = [];
        while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
          
          //Sản phẩm
          $sqlSP = "SELECT *	FROM sanpham Where sp_ma=" . $row['sp_ma'];        
          $resultSP = mysqli_query($conn, $sqlSP);
          $SP = mysqli_fetch_array($resultSP, MYSQLI_ASSOC);
          $tenSP = $SP['sp_ten'];
          //Đơn đặt hàng
          $sqldh = "SELECT *	FROM dondathang Where dh_ma=" . $row['dh_ma'];        
          $resultdh = mysqli_query($conn, $sqldh);
          $dh = mysqli_fetch_array($resultdh, MYSQLI_ASSOC);
          $kh_tendangnhap = $dh['kh_tendangnhap'];
          $ds_sanpham_dondathang[] = array(
            'sp_ma' => $row['sp_ma'],
            'tenSP' => $tenSP,
            'dh_ma' => $row['dh_ma'],
            'kh_tendangnhap' => $kh_tendangnhap,
            'sp_dh_soluong' => $row['sp_dh_soluong'],
            'sp_dh_dongia' => $row['sp_dh_dongia']
            
          );
        }
        ?>

        <!-- Nút thêm mới, bấm vào sẽ hiển thị form nhập thông tin Thêm mới -->
        <a href="create.php" class="btn btn-primary">Thêm mới</a>
        <table id="tableSP" class="table table-bordered table-hover mt-2">
          <thead class="thead-dark">
          <tr>
              <th>STT</th>
              <th>Mã đơn hàng</th>
              <th>Tên sản phẩm</th>              
              <th>Khách hàng</th>
              <th>Số lượng</th>
              <th>Đơn giá</th>
              <th>Hành động</th>
          </tr>
          </thead>
          <tbody>
            <?php
              foreach ($ds_sanpham_dondathang as $sanpham_dondathang):?>
                <tr>
                  <td><?= $stt; $stt++?></td>
                  <td><?= $sanpham_dondathang['dh_ma']?></td>
                  <td><?= $sanpham_dondathang['tenSP']?></td>                  
                  <td><?= $sanpham_dondathang['kh_tendangnhap']?></td>
                  <td><?= $sanpham_dondathang['sp_dh_soluong']?></td>
                  <td><?= $sanpham_dondathang['sp_dh_dongia']?></td>
                  <td>
                    <!-- Nút sửa, bấm vào sẽ hiển thị form hiệu chỉnh thông tin dựa vào khóa chính `lsp_ma` -->
                    <a class="btn btn-warning">
                      <span data-feather="edit"></span> Sửa
                    </a>
                    <!-- Nút xóa, bấm vào sẽ xóa thông tin dựa vào khóa chính `sp_ma` , `dh_ma` -->
                    
                    <button class="btn btn-danger btnDelete">Xóa</button>
                  </td>
                  
                </tr>
              <?php endforeach ?>
          </tbody>
        
        </table>
        <!-- End block content -->
      </main>
    </div>
  </div>

  <!-- footer -->
  <?php include_once(__DIR__ . '/../../layouts/partials/footer.php'); ?>
  <!-- end footer -->

  <!-- Nhúng file quản lý phần SCRIPT JAVASCRIPT -->
  <?php include_once(__DIR__ . '/../../layouts/scripts.php'); ?>

  <!-- DataTable JS -->
  <script src="/king/assets/vendor/DataTables/datatables.min.js"></script>
  <script src="/king/assets/vendor/DataTables/Buttons-1.6.5/js/buttons.bootstrap4.min.js  "></script>
  <script src="/king/assets/vendor/DataTables/pdfmake-0.1.36/pdfmake.min.js"></script>
  <script src="/king/assets/vendor/DataTables/pdfmake-0.1.36/vfs_fonts.js"></script>
  <!-- SweetAlert -->
  <script src="/king/assets/vendor/sweetalert/sweetalert.min.js"></script>

  <script>
    $(document).ready( function () {  
      $('#tableSP').on('draw.dt', function () {
            console.log('draw.dt');
            eventFiredBtnDeleteSweetAlert(this);
        }).DataTable({    
          responsive: false,   
          dom: 'Blfrtip',
          buttons: [
              'copy', 'excel', 'pdf'
          ]
        });
  } );
    </script>
</body>

</html>
<?php 
  else: echo ('<script>location.href = "/king/index.php";</script>');
  endif;
  ?>