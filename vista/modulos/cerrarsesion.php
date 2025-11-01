<?php

session_destroy();
echo '<script>
localStorage.removeItem("modalMostrado");
window.location = "ingreso";
</script>';