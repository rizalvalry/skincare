<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
    <!-- Header -->
    <div class="header bg-primary pb-6">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <h6 class="h2 text-white d-inline-block mb-0">Kelola Banners</h6>
              <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                  <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                  <li class="breadcrumb-item"><a href="<?php echo site_url('admin/banners'); ?>">Banners</a></li>
                </ol>
              </nav>
            </div>
            <div class="col-lg-6 col-5 text-right">
              <a href="<?php echo site_url('admin/banners/add_new_banner'); ?>" class="btn btn-sm btn-neutral">Tambah</a>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Page content -->
    <div class="container-fluid mt--6">
      <div class="row">
        <div class="col">
          <div class="card bg-default shadow">
            <!-- Card header -->
            <div class="card-header bg-transparent border-0">
              <h3 class="mb-0 text-light">Slide Banners</h3>
            </div>

            <div class="packageContainer">
            <!-- Light table -->
            <div class="table-responsive">
              <table class="table align-items-center table-dark table-flush" id="packageList" style="width: 100%">
                <thead class="thead-dark">
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">title</th>
                    <th schope="col">subtitle</th>
                    <th schope="col">picture_name</th>
                    <th schope="col">Button Text</th>
                    <th schope="col">Status</th>
                    <th scope="col"></th>
                  </tr>
                </thead>
              </table>
            </div>
          </div>

          </div>
        </div>
      </div>


<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="modal-default" aria-hidden="true">
  <div class="modal-dialog modal-modal-dialog-centered modal-" role="document">
      <div class="modal-content">
          <div class="modal-header">
              <h6 class="modal-title" id="modal-title-default">Hapus Banner</h6>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true">×</span>
              </button>
          </div>
          <form action="#" id="deleteBanner" method="POST">

            <input type="hidden" name="id" value="" class="deleteID">

          <div class="modal-body">
              <p>Yakin ingin menghapus? Tindakan ini tidak dapat dibatalkan.</p>
          </div>
          <div class="modal-footer">
              <button type="submit" class="btn btn-danger btn-delete">Hapus</button>
              <button type="button" class="btn btn-link  ml-auto" data-dismiss="modal">Batal</button>
          </div>
          </form>
      </div>
  </div>
</div>




<link href="<?php echo get_theme_uri('vendor/datatables.net-bs4/css/dataTables.bootstrap4.min.css', 'argon'); ?>" rel="stylesheet">

<script src="<?php echo get_theme_uri('vendor/datatables.net/js/jquery.dataTables.min.js', 'argon'); ?>"></script>
<script src="<?php echo get_theme_uri('vendor/datatables.net-bs4/js/dataTables.bootstrap4.min.js', 'argon'); ?>"></script>
<script src="<?php echo base_url('assets/plugins/datatables.lang.js'); ?>"></script>
<script>
  $(document).ready(function() {
    $(document).on('click', '.btnDelete', function() {
      var id  = $(this).data('id');

      $('.deleteID').val(id);
      $('#deleteModal').modal('show');
    });

    $('#deleteBanner').submit(function(e) {
      e.preventDefault();

      var id = $('.deleteID').val();
      var btn = $('.btn-delete');

      btn.html('<i class="fa fa-spin fa-spinner"></i> Menghapus...');

      $.ajax({
        method: 'POST',
        url: '<?php echo site_url('admin/banners/banner_api?action=delete_banner'); ?>',
        data: {
            id: id
        },
        success: function (res) {
          if (res.code == 204) {
            btn.html('<i class="fa fa-check"></i> Terhapus!');

            setTimeout(() => {
              $('#deleteModal').modal('hide');
              table.ajax.reload();
              btn.html('Hapus');
            }, 1500);
          }
        }
      })
    });

    var table = $('#packageList').DataTable({
      "ajax" : "<?php echo site_url('admin/banners/banner_api?action=banner_list'); ?>",
      "columns" : [
        {
            "data": "id",
            render: function (data, type, row, meta) {
                return meta.row + meta.settings._iDisplayStart + 1;
            }
        },
        {"data": "title"},
        {"data": "subtitle"},
        {"data": "picture_name","render": function(data, type, row) {
                    return '<img src="<?php echo base_url('assets/uploads/banners/'); ?>'+data+' "class="img-thumbnail" />';}},
        {"data": "button_text"},
        {"data": "is_active"},
        {"mRender": function (data, type, row) {
          return '<div class="text-right"><a href="<?php echo site_url('admin/banners/edit/'); ?>'+row.id+'" class="btn btn-warning btn-sm btnEdit"><i class="fa fa-edit"></i></a><a href="#" data-id="'+row.id+'" class="btn btn-danger btn-sm btnDelete"><i class="fa fa-trash"></i></a></div>';}
        }
      ],
      "language" : {
        "search" : "Cari:",
        "lengthMenu" : "Menampilkan _MENU_ data",
        "info" : "Menampilkan _START_ sampai _END_ data dari _TOTAL_ data",
        "infoEmpty" : "Tidak ada data yang ditampilkan",
        "infoFiltered" : "(dari total _MAX_ data)",
        "zeroRecords" : "Tidak ada hasil pencarian ditemukan",
        "paginate": {
          "first":"&laquo;",
          "last":"&raquo;",
          "next":       "&rsaquo;",
          "previous":   "&lsaquo;"
        },
      }
    });


  });
</script>