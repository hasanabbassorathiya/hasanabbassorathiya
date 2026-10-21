<?php
header('Set-Cookie: admin_token=; path=/admin; max-age=0');
header('Location: index.php');
echo '<script>location.href="index.php"</script>';