<!-- Header -->
<?php $this->load->view('_partials_admin/header'); ?>
<!-- End Header -->

<style>
table tr>th {
    text-align: center;
}
</style>

<!-- Navbar -->
<?php $this->load->view('_partials_admin/navbar'); ?>
<!-- End Navbar -->

<!-- Sidebar -->
<?php $this->load->view('_partials_admin/sidebar'); ?>
<!-- End Sidebar -->

<div class="content-wrapper">
    <section class="content-header">
    <!-- Breadcrumb -->
    <?php $this->load->view('_partials_admin/breadcrumb'); ?>
    <!-- End Breadcrumb -->
    </section>
    <section class="content">
        <div class="box box-solid">
            <div class="box-header with-border">
                <h3 style="margin-top:0px"><i class="fa fa-user"></i> Seniman</h3>
            </div>    
            <div class="box-body">
                <table class="table table-bordered table-hover table-striped" id="tabel-publisher" style="margin-bottom: 10px">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Seniman</th>
                            <th>No Telepon</th>
                            <th>Email</th>
                            <th>Status User</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $start = 0;
                        foreach ($publishers as $publisher) {?>
                            <tr>
                                <td style="text-align:center"><?= ++$start ?></td>
                                <td><?= $publisher->name ?></td>
                                <td style="text-align:center"><?= $publisher->business_number ?></td>
                                <td style="text-align:center"><?= $publisher->business_email ?></td>
                                <td style="text-align: center">
                                    <?php if($publisher->status == "submitted"): ?>
                                        <label class="badge bg-yellow"><?= $publisher->status ?></label>
                                    <?php elseif($publisher->status == "approved"): ?>
                                        <label class="badge bg-green"><?= $publisher->status ?></label>
                                    <?php else: ?>
                                        <label class="badge bg-danger"><?= $publisher->status ?></label>
                                    <?php endif ?>
                                </td>
                                <td style="text-align:center" width="200px">
                                    <a href="<?= site_url('admin/peserta/'.$publisher->id) ?>" class="btn-xs btn-primary"><i class="fa fa-eye"></i> Detail</a>
                                    <a href="<?= site_url('admin/publisher/' . $publisher->id . '/hapus') ?>"
                                    class="btn btn-danger" onclick="return confirm('Apakah anda ingin menghapus ?')"
                                    data-toggle="tooltip" data-placement="top" title="Hapus">
                                    <i class="fa fa-trash-o"></i>
                                </a> <br>
                                    <?php if ($publisher->status == 'approved') { ?>
                                        <a href="#" class="btn btn-sm btn-success" disabled>Buka</a>
                                        <a href="<?= site_url('admin/publisher/'.$publisher->id.'/block') ?>" class="btn btn-sm btn-github" onclick="return confirm('Apakah ingin menonaktifkan seniman ini ?')">Blokir</a>
                                    <?php } else { ?>
                                        <a href="<?= site_url('admin/publisher/'.$publisher->id.'/unblock') ?>" class="btn btn-sm btn-success" onclick="return confirm('Apakah ingin mengaktifkan seniman ini ?')">Buka</a>
                                        <a href="#" class="btn btn-sm btn-github" disabled>Blokir</a>
                                    <?php } ?>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<!-- JS -->
<?php $this->load->view('_partials_admin/js'); ?>
<!-- End JS -->
<script>
  $(function () {
    $('#tabel-publisher').DataTable()
  })
</script>
<!-- Footer -->
<?php $this->load->view('_partials_admin/footer'); ?>
<!-- End Footer -->