<?php
require 'config/config.php';
require 'config/Database.php';
$db = new Database(); //base de datos
$con = $db->conectar(); // la conexion

$id = isset($_GET['id']) ? $_GET['id'] : '';
$token = isset($_GET['token']) ? $_GET['token'] : '';

if ($id == '' || $token == ''){
  echo 'Error al procesar la petición';
  exit;
} else {

  $token_tmp = hash_hmac('sha1', $id, KEY_TOKEN);

  if($token == $token_tmp){

    $sql = $con->prepare("SELECT count(id) FROM Productos WHERE id=? AND Activo=1"); //Traer solicitudes preparadas
    $sql->execute([$id]);
    if($sql->fetchColumn() > 0) {

      $sql = $con->prepare("SELECT nombre, descripcion, precio, descuento FROM Productos WHERE id=? AND Activo=1 LIMIT 1"); //Traer solicitudes preparadas
      $sql->execute([$id]);
      $row = $sql->fetch(PDO::FETCH_ASSOC);
      $nombre = $row['nombre'];
      $descripcion = $row['descripcion'];
      $precio = $row['precio'];
      $descuento = $row['descuento'];
      $precio_desc = $precio - (($precio * $descuento) / 100);
      $dir_images = 'images/productos/'. $id .'/';

      $rutaImg = $dir_images . 'principal.jpg';

      if(!file_exists($rutaImg)){
        $rutaImg = 'images/no-photo.jpg';
      }
      
      $imagenes = array();
      $dir = dir($dir_images);

      while (($archivo = $dir->read()) != false) {
          if ($archivo != 'principal.jpg' && (strpos($archivo, 'jpg') || strpos($archivo, 'jpeg'))) {
              $imagenes[] = $dir_images . $archivo;
          }
      
      }
    }

  } else {
      echo 'Error al procesar la petición';
      exit;
  
  }


}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sport Shop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-aFq/bzH65dt+w6FI2ooMVUpc+21e0SRygnTpmBvdBgSdnuTN7QbdgL+OapgHtvPp" crossorigin="anonymous">
    <link href="css/estilo.css" rel="stylesheet">
</head>

<body>
  
   
<header>
    
  <div class="navbar navbar-expand.lg navbar-dark bg-dark">
    <div class="container">
      <a href="#" class="navbar-brand">
        <strong>Sport Shop</strong>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarHeader" aria-controls="navbarHeader" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <div class="collapse navbar-collapse" id="navbarHeader">

      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
            <a href="#" class="nav-link active">Catalago</a>

        </li>

        <li class="nav-item">
            <a href="#" class="nav-link">Contacto</a>

        </li>

      </ul>

      <a href="carrito.php"class="btn btn-primary">Carrito</a >
        
      </div>

    </div>
  </div>
</header>

<main>
    <div class="container">
        <div class="row">
            <div class="col-md-6 order-md-1">
                <div id="carouselImages" class="carousel slide">
                    <div class="carousel-inner">
                      <div class="carousel-item active">
                        <img src="<?php echo $rutaImg; ?>" class="d-block w-100">
                      </div>

                      <?php foreach($imagenes as $img) { ?>
                      <div class="carousel-item">
                          <img src="<?php echo $img; ?>" class="d-block w-100">
                          

                          </div>
                          <?php } ?>

                    </div>
                    <button class="carousel-control-prev" type="button" data-bs-target="#carouselImages" data-bs-slide="prev">
                      <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Previous</span>
                    </button>
                    <button class="carousel-control-next" type="button" data-bs-target="#carouselImages" data-bs-slide="next">
                      <span class="carousel-control-next-icon" aria-hidden="true"></span>
                      <span class="visually-hidden">Next</span>
                    </button>
                  </div>
                

            
              

            </div>
            <div class="col-md-6 order-md-2">
              <h2><?php echo $nombre; ?></h2>

              <?php if($descuento > 0) {  ?>
                  <p><del><?php echo MONEDA . number_format($precio, 2, '.', ',');  ?></del></p>
                  <h2> 
                      <?php echo MONEDA . number_format($precio_desc, 2, '.', ',');  ?> </h2>
                  <small class="text-success"><?php echo $descuento ?>% descuento</small>
                  </h2>

              <?php  } else { ?>


                   <h2> <?php echo MONEDA . number_format($precio, 2, '.', ',');  ?></h2>
              
              <?php  } ?>

              <p class="lead"> 
                <?php echo $descripcion; ?>
              </p>

              <div class="d-grid gap-3 col-10 mx-auto">
                <button class="btn btn-primary" type="button">Comprar ahora</button>
                <button class="btn btn-outline-primary" type="button">Agregar al carrito</button>

              </div>


            </div>
        </div>
    
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js" integrity="sha384-qKXV1j0HvMUeCBQ+QVp7JcfGl760yU08IQ+GpUo5hlbpg51QRiuqHAJz8+BrxE/N" crossorigin="anonymous"></script>
</body>
</html> 