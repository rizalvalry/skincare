<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
    <!-- Header -->
    <div class="header bg-primary pb-6">
      <div class="container-fluid">
        <div class="header-body">
          <div class="row align-items-center py-4">
            <div class="col-lg-6 col-7">
              <h6 class="h2 text-white d-inline-block mb-0">Tambah Banner</h6>
            </div>
            <div class="col-lg-6 col-5 text-right">
              <nav aria-label="breadcrumb" class="d-none d-md-inline-block ml-md-4">
                <ol class="breadcrumb breadcrumb-links breadcrumb-dark">
                  <li class="breadcrumb-item"><a href="<?php echo site_url('admin'); ?>"><i class="fas fa-home"></i></a></li>
                  <li class="breadcrumb-item"><a href="<?php echo site_url('admin/banners'); ?>">Banner</a></li>
                  <li class="breadcrumb-item active" aria-current="page">Tambah</li>
                </ol>
              </nav>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Page content -->
    <div class="container-fluid mt--6">
      <?php echo form_open_multipart('admin/banners/add_banner'); ?>

      <div class="row">
        <div class="col-md-8">
          <div class="card-wrapper">
            <div class="card">
              <div class="card-header">
                <h3 class="mb-0">Data Banner</h3>
                <?php if ($flash) : ?>
                    <div class="alert alert-primary alert-dismissible fade show" role="alert">
                        <span class="alert-icon"><i class="ni ni-like-2"></i></span>
                        <span class="alert-text"><strong><?php echo $flash; ?></strong></span>
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                <?php endif; ?>
              </div>
        
              <div class="card-body">

              <div class="form-group">
                  <label class="form-control-label" for="title">Title:</label>
                  <textarea name="title" class="form-control" id="title"><?php echo set_value('title'); ?></textarea>
                  <?php echo form_error('title'); ?>
                </div>

                <div class="form-group">
                  <label class="form-control-label" for="Subtitle">Subtitle:</label>
                  <input type="text" name="subtitle" value="<?php echo set_value('subtitle'); ?>" class="form-control" id="subtitle">
                  <?php echo form_error('subtitle'); ?>
                </div>

                <div class="row">
                  <div class="col-6">
                    <div class="form-group">
                      <label class="form-control-label" for="button_text">Button Text:</label>
                      <input type="text" name="button_text" value="<?php echo set_value('button_text'); ?>" class="form-control" id="button_text">
                      <?php echo form_error('button_text'); ?>
                    </div>
                  </div>
                  <div class="col-6">
                 
                  </div>
                </div>

                <div class="row">
                  <div class="col-6">
                    <div class="form-group">
                      <label class="form-control-label" for="stock">Warna Button </label>
                      <input type="color" id="favcolor" name="warna" value="<?php echo set_value('unit'); ?>">
                      <!-- <input type="text" name="stock" value="<?php echo set_value('stock'); ?>" class="form-control" id="stock"> -->
                      <?php echo form_error('stock'); ?>
                    </div>
                  </div>
                  <div class="col-6">
                    <div class="form-group">
                      <label class="form-control-label" for="unit">Warna General </label>
                      <input type="color" id="favcolor" name="warna" value="<?php echo set_value('unit'); ?>">
                      <!-- <input type="text" name="unit" value="<?php echo set_value('unit'); ?>" class="form-control" id="unit"> -->
                      <?php echo form_error('unit'); ?>
                    </div>
                  </div>
                </div>
              
                <div class="btn-group btn-group-toggle" data-toggle="buttons">
                <label class="btn btn-secondary active">
                  <input type="radio" name="is_active" value="1" id="is_active" autocomplete="off" checked> Active
                </label>
                <label class="btn btn-secondary">
                  <input type="radio" name="is_active" value="0" autocomplete="off"> DisActive
                </label>
              </div>

              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="mb-0">Foto</h3>
                </div>
                <div class="card-body">
                   <div class="form-group">
                     <label class="form-control-label" for="pic">Foto:</label>
                     <input type="file" name="picture" class="form-control" id="pic">
                     <small class="text-muted">Pilih foto PNG atau JPG dengan ukuran maksimal 2MB</small>
                   </div>
                </div>
                <div class="card-footer text-right">
                    <input type="submit" value="Tambah Banner Baru" class="btn btn-primary">
                </div>
            </div>
        </div>
      </div>

    </form>