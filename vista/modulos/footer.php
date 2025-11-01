<footer class="main-footer">
<div class="float-right d-none d-sm-block">
    <p>Todos los derechos reservados.</p>
</div>
<?php 
if(isset($_SESSION["n_empresa"])): ?>
    <p>Copyright &copy; 2024 <a href="#" target="_blank"><?= $_SESSION["n_empresa"] ?></a></p>
<?php else: ?>
    <p>Copyright &copy; 2024 <a href="#" target="_blank">SAVYC</a></p>
<?php endif; ?>
    
</footer>

<!-- Control Sidebar -->
<aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
</aside>
<!-- /.control-sidebar -->
</div>
<!-- ./wrapper -->