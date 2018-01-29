<?php
require_once 'vendor/autoload.php';

$app=new \Slim\Slim();

$db = new mysqli('localhost','root','sir.rotvico','curso_angular4');

$app->get("/pruebas",function() use($app,$db){
	echo "Hola mundo desde Slim";
	var_dump($db);

});

$app->get("/probando",function() use($app){
	echo "OTRO TEXTO";

});

//LISTAR PRODUCTOS

$app -> get('/productos',function() use ($db, $app){
	$sql = 'SELECT * FROM productos ORDER BY id DESC;';
	$query = $db->query($sql);

	$productos= array ();

	while ($producto =$query->fetch_assoc()) {
		$productos[]=$producto;
		# code...
	}

	$result = array(
			'status' => 'success',
			'code' => 200,
			'data' => $productos
	);

	echo json_encode($result);

});

//DEVOLVER UN SOLO PRODUCTO

$app -> get('/producto/:id',function($id) use ($db, $app){
	$sql = 'SELECT * FROM productos where id ='. $id.';';
	$query = $db->query($sql);

	$result = array(
			'status' => 'error',
			'code'   => 404,
			'messag' => 'Producto no encontrado'
	);
	if ($query->num_rows ==1){
		$producto = $query->fetch_assoc();
		$result = array(
			'status' => 'success',
			'code'   => 200,
			'data' => $producto
		);	

	}

	echo json_encode($result);

});

//ELIMINAR UN PRODUCTO

$app -> get('/delete-producto/:id',function($id) use ($db, $app){
	$sql = 'DELETE FROM productos where id ='. $id.';';
	$query = $db->query($sql);

	if ($query){
		$result = array(
			'status' => 'success',
			'code'   => 200,
			'message' => 'el producto se ha eliminado'
		);	

	}
	else {

		$result = array(
				'status' => 'error',
				'code'   => 404,
				'message' => 'Producto no se elimino'
		);

	}

	echo json_encode($result);

});

//ACTUALIZAR UN PRODUCTO

$app->post('/update-producto/:id', function($id) use ($db, $app){
	$json=$app->request->post('json');
	$data = json_decode($json,true);

	$sql = "UPDATE productos SET ".
			"nombre = '{$data["nombre"]}', ".
			"descripcion = '{$data["descripcion"]}', ";

	if (isset($_FILES['uploads'])){

		$piramideUploader = new PiramideUploader();
		$upload = $piramideUploader->upload('image', "uploads", "uploads", array('image/jpeg', 'image/png', 'image/gif'));
		$file = $piramideUploader->getInfoFile();
		$file_name = $file['complete_name'];

		if (isset($upload) && $upload["uploaded"] == false){
			$result = array(
					'status' => 'error',
					'code'   => 404,
					'message' => 'El archivo no ha podido subirse.'
			);
			

		}else{
			$result = array(
					'status' => 'success',
					'code'   => 200,
					'message' => 'El archivo se ha subido',
					'filename' => $file_name

			);	
			$sql .="imagen = '$file_name', ";	

		}
	}

	
		
	$sql .= "precio = '{$data["precio"]}' WHERE id={$id}";

	$query=$db->query($sql);

	var_dump($sql);

	if($query){
		$result = array(
				'status' => 'success',
				'code'   => 200,
				'message' => 'Producto modificado exitosamente'
		);

	}else{
		$result = array(
				'status' => 'error',
				'code'   => 404,
				'message' => 'Producto no modificado. Algo salio terriblemente mal'.$sql
		);

	}

	echo json_encode($result);


});

//SUBIR UNA IMAGEN A UN PRODUCTO
$app->post('/upload-file', function() use ($db, $app){
	$result = array(
					'status' => 'error',
					'code'   => 404,
					'message' => 'El archivo no ha podido subirse.'
			);
	
	if (isset($_FILES['uploads'])){

		$piramideUploader = new PiramideUploader();
		$upload = $piramideUploader->upload('image', "uploads", "uploads", array('image/jpeg', 'image/png', 'image/gif'));
		$file = $piramideUploader->getInfoFile();
		$file_name = $file['complete_name'];

		if (isset($upload) && $upload["uploaded"] == false){
			$result = array(
					'status' => 'error',
					'code'   => 404,
					'message' => 'El archivo no ha podido subirse.'
			);
			

		}else{
			$result = array(
					'status' => 'success',
					'code'   => 200,
					'message' => 'El archivo se ha subido',
					'filename' => $file_name

			);		

		}
	}

	echo json_encode($result);

});

//GUARDAR PRODUCTOS

$app->post('/productos',function() use ($app,$db){
	$json=$app->request->post('json');
	$data=json_decode($json,true);

	if(!isset($data['nombre'])){
		$data['nombre']=null;

	}

	if(!isset($data['descripcion'])){
		$data['descripcion']=null;

	}

	if(!isset($data['precio'])){
		$data['precio']=null;

	}

	if(!isset($data['imagen'])){
		$data['imagen']=null;

	}

	$query="INSERT INTO productos VALUES (NULL,".
	"'{$data['nombre']}',".
	"'{$data['descripcion']}',".
	"'{$data['precio']}',".
	"'{$data['imagen']}'".
	");";

	$result = array(
			'status' => 'error',
			'code' => 404,
			'mesage' => 'el iiiiiiiproducto no se ha creado correctamente'.$query
			 );

	$insert=$db->query($query);

	

	

	if($insert){
		$result = array(
			'status' => 'success',
			'code' => 200,
			'mesage' => 'producto creado correctamente'
			 );

	}
	echo json_encode($result);
});

$app->run();