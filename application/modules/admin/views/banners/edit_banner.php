<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <!-- Header -->
    <div class="header bg-primary pb-6">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <h6 class="h2 text-white d-inline-block mb-0">Edit Produk</h6>
            </div>
            <div class="col-lg-6 col-5 text-right">
              <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                  <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                  <li class="breadcrumb-item"><a href="<?php echo site_url('admin/banners'); ?>">Banner</a></li>
                  <li class="breadcrumb-item"><a href="#"><?php echo $banner->title; ?></a></li>
                  <li class="breadcrumb-item active" aria-current="page">Edit</li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--6">
      <?php echo form_open_multipart('admin/banners/edit_banner'); ?>
      <input type="hidden" name="id" value="<?php echo $banner->id; ?>">

      <div class="row">
        <div class="col-md-8">
          <div class="card-wrapper">
            <div class="card">
              <div class="card-header">
                <h3 class="mb-0">Data Produk</h3>
                <?php if ($flash) : ?>
                    <div class="alert alert-default alert-dismissible fade show" role="alert">
                        <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                        <span class="alert-text"><strong><?php echo $flash; ?></strong></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
              </div>
        
              <div class="card-body">
                <div class="row">
                  <div class="col-md-12">
                    
                  </div>
                </div>

                <div class="form-group">
                  <label class="form-control-label" for="title">Title:</label>
                  <input type="text" name="title" value="<?php echo set_value('name', $banner->title); ?>" class="form-control" id="title">
                  <?php echo form_error('title'); ?>
                </div>

                <div class="row">
                  <div class="col-6">
                    <div class="form-group">
                      <label class="form-control-label" for="subtitle">Subtitle:</label>
                      <div class="input-group mb-3">
                        <div class="input-group-prepend">
                          <span class="input-group-text">Rp</span>
                        </div>
                        <input type="text" name="subtitle" value="<?php echo set_value('subtitle', $banner->subtitle); ?>" class="form-control" id="subtitle">
                      </div>
                        <?php echo form_error('subtitle'); ?>
                    </div>
                  </div>
                  <div class="col-6">
                  <div class="form-group">
                  <label class="form-control-label" for="button_text">Button Text:</label>
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text">Rp</span>
                    </div>
                    <input type="text" name="button_text" value="<?php echo set_value('button_text', $banner->button_text); ?>" class="form-control" id="button_text">
                  </div>
                    <?php echo form_error('button_text'); ?>
                </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="av" class="form-control-label">
                    <input type="checkbox" id="av" name="is_active" value="1" <?php echo set_checkbox('is_active', $banner->is_active, ($banner->is_active == 1) ? TRUE : FALSE); ?>> Apakah produk ini tersedia?
                  </label>
                </div>
              
              </div>
              
            </div>
            
          </div>

        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <div class="row">
                        <div class="col-4">
                            <h3 class="mb-0">Foto</h3>
                        </div>
                        <?php if ($banner->picture_name) : ?>
                        <div class="col-8">
                            <ul class="nav nav-pills mb-3 float-right" id="pills-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link p-1 active" id="pills-current-tab" data-toggle="pill" href="#pills-current" role="tab" aria-controls="pills-home" aria-selected="true">Current</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link p-1" id="pills-edit-tab" data-toggle="pill" href="#pills-edit" role="tab" aria-controls="pills-profile" aria-selected="false">Ganti</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link p-1" id="pills-delete-tab" data-toggle="pill" href="#pills-delete" role="tab" aria-controls="pills-contact" aria-selected="false">Hapus</a>
                                </li>
                            </ul>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body">
                    <?php if ($banner->picture_name != NULL) : ?>
                    <div class="tab-content" id="pills-tabContent">
                        <div class="tab-pane fade show active" id="pills-current" role="tabpanel" aria-labelledby="pills-home-tab">
                            <div class="text-center">
                                <img alt="<?php echo $banner->title; ?>" src="<?php echo base_url('assets/uploads/banners/'. $banner->picture_name); ?>" class="img img-fluid rounded">
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-edit" role="tabpanel" aria-labelledby="pills-profile-tab">
                            <div class="form-group">
                                <label class="form-control-label" for="pic">Foto:</label>
                                <input type="file" name="picture" class="form-control" id="pic">
                                <small class="text-muted">Pilih foto PNG atau JPG dengan ukuran maksimal 2MB</small>
                                <small class="newUploadText">Unggah file baru untuk mengganti foto saat ini.</small>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="pills-delete" role="tabpanel" aria-labelledby="pills-contact-tab">
                            <p class="deleteText">Klik link dibawah untuk menghapus foto. Tindakan ini tidak dapat dibatalkan.</p>
                            <div class="text-right">
                                <a href="#" class="deletePictureBtn btn btn-danger">Hapus</a>
                            </div>
                        </div>
                    </div>
                    <?php else : ?>
                    <div class="form-group">
                        <label class="form-control-label" for="pic">Foto:</label>
                        <input type="file" name="picture" class="form-control" id="pic">
                        <small class="text-muted">Pilih foto PNG atau JPG dengan ukuran maksimal 2MB</small>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="card-footer text-right">
                    <input type="submit" value="Simpan" class="btn btn-primary">
                </div>
            </div>
        </div>
      </div>

    </form>
    
    <script>
        $('.deletePictureBtn').click(function(e) {
            e.preventDefault();

            $(this).html('<i class="fa fa-spin fa-spinner"></i> Menghapus...');

            $.ajax({
                method: 'POST',
                url: '<?php echo site_url('admin/products/product_api?action=delete_image'); ?>',
                data: {
                    id: <?php echo $banner->id; ?>
                },
                context: this,
                success: function(res) {
                    if (res.code == 204) {
                        $('.deleteText').text('Gambar berhasil dihapus. Produk ini akan menggunakan gambar default jika tidak ada gambar baru yang diunggah');
                        $(this).html('<i class="fa fa-check"></i> Terhapus!');

                        setTimeout(function() {
                            $('.newUploadText').text('Pilih gambar baru untuk mengganti gambar yang dihapus');
                            $('#pills-delete, #pills-delete-tab, #pills-current, #pills-current-tab').hide('fade');
                            $('#pills-edit').tab('show');
                            $('#pills-edit-tab').addClass('active').text('Upload baru');
                        }, 3000);
                    }
                    else {
                        console.log('Terdapat kesalahan');
                    }
                }
            })
        });
    </script>