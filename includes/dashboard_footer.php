<?php
declare(strict_types=1);
?>
    </div> <!-- Close dashboard-content -->
</div> <!-- Close dashboard-wrapper -->

<!-- Mobile Bottom Navigation (Only visible on mobile) -->
<div class="mobile-bottom-nav d-md-none">
    <a href="<?= get_base_url() ?>/dashboard/employee/index.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-house"></i>
        <span>Home</span>
    </a>
    <a href="<?= get_base_url() ?>/dashboard/employee/documents.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'documents.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-folder-open"></i>
        <span>Docs</span>
    </a>
    
    <!-- Floating Action Button in Center -->
    <div class="nav-item-fab">
        <a href="<?= get_base_url() ?>/dashboard/employee/id_card.php" class="fab-button shadow-lg">
            <i class="fa-solid fa-qrcode text-white"></i>
        </a>
    </div>

    <a href="<?= get_base_url() ?>/dashboard/employee/communications.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'communications.php' ? 'active' : '' ?>">
        <i class="fa-regular fa-bell"></i>
        <span>Updates</span>
    </a>
    <a href="<?= get_base_url() ?>/dashboard/employee/settings.php" class="nav-item <?= basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : '' ?>">
        <i class="fa-solid fa-gear"></i>
        <span>More</span>
    </a>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- DataTables JS & Buttons Extensions -->
<script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- App custom JS -->
<script src="<?= get_base_url() ?>/assets/js/app.js"></script>

<script>
$(document).ready(function() {
    // Universal DataTables Initialization with Export Options
    $('.datatable-export').DataTable({
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                className: 'btn btn-success btn-sm me-1',
                text: '<i class="fa-solid fa-file-excel me-1"></i> Excel'
            },
            {
                extend: 'pdfHtml5',
                className: 'btn btn-danger btn-sm me-1',
                text: '<i class="fa-solid fa-file-pdf me-1"></i> PDF'
            },
            {
                extend: 'csvHtml5',
                className: 'btn btn-info btn-sm text-white me-1',
                text: '<i class="fa-solid fa-file-csv me-1"></i> CSV'
            },
            {
                extend: 'print',
                className: 'btn btn-primary btn-sm',
                text: '<i class="fa-solid fa-print me-1"></i> Print'
            }
        ],
        responsive: true
    });
});
</script>
</body>
</html>
