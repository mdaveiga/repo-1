<?php

$headers = array(
    'Content-Type:application/json',
    'Authorization: Basic ZTUwZGIyODk3OTYyOWMwYmIzNTRlYTE2MTNkMmYxNDBlOTM2YmE3YWYyM2Y5MTgxODNlMjZiYjFkZGY0YjJkYjpiNGU3ZmU0ZjM3MGEyYTk2MjFiZDY2YjRmOTE2NGUzNGNhZjQ0NmMxNGY3OWUwODk1NDViZDRlMTYzODI4YWMx'
);

$url = 'https://api.planningcenteronline.com/people/v2/lists/400847/people?include=phone_numbers&order=name&per_page=100';
$curl = curl_init($url);
curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//raw output
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

$result = json_decode(curl_exec($curl),true);
curl_close($curl);

$lista = array();
$paginas = ceil($result["meta"]["total_count"]/100);

for ($i=0; $i <= $paginas-1; $i++) {

    if($i > 0){
        $nextPage = 100 * $i + 1;
        $url = 'https://api.planningcenteronline.com/people/v2/lists/400847/people?include=phone_numbers&order=name&per_page=100&offset='.$nextPage;
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);//raw output
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');

        $result = json_decode(curl_exec($curl),true);
        curl_close($curl);
    }

	$pasadas = 1;
	foreach ($result['data'] as $key) {
	
		if($pasadas == 1){	
			$mes = substr($key["attributes"]["birthdate"], -5, 2);
			$mes_nombre = "";
			
			switch ($mes) {
				case "01":
					$mes_nombre = "Enero ";
					break;
				case "02":
					$mes_nombre = "Febrero ";
					break;
				case "03":
					$mes_nombre = "Marzo ";
					break;
				case "04":
					$mes_nombre = "Abril ";
					break;
				case "05":
					$mes_nombre = "Mayo ";
					break;
				case "06":
					$mes_nombre = "Junio ";
					break;
				case "07":
					$mes_nombre = "Julio ";
					break;
				case "08":
					$mes_nombre = "Agosto ";
					break;
				case "09":
					$mes_nombre = "Septiembre ";
					break;
				case "10":
					$mes_nombre = "Octubre ";
					break;
				case "11":
					$mes_nombre = "Noviembre ";
					break;
				case "12":
					$mes_nombre = "Diciembre ";
					break;
			}
		}
		
		$pasadas++;
		
        $telefono_celular = "";
        $telefono_home = "";
        
        foreach ($result["included"] as $tel) {
            
            if($tel["id"] == $key["relationships"]["phone_numbers"]["data"][0]["id"] || $tel["id"] == $key["relationships"]["phone_numbers"]["data"][1]["id"] ){
                if($tel["attributes"]["location"] == "Mobile"){
                    $telefono_celular = $tel["attributes"]["number"];
                }
                if($tel["attributes"]["location"] == "Home"){
                    $telefono_home = $tel["attributes"]["number"];
                }
            }                       
        }

		$cumpleanos = new DateTime($key["attributes"]["birthdate"]);
		$hoy = new DateTime();
		$annos = $hoy->diff($cumpleanos);
	
        $lista[] = array(
            'foto' => $key["attributes"]["avatar"],
            'nombre' => $key["attributes"]["name"],
            'edad' => $annos,
            'celular' => $telefono_celular,			
            'dia' => substr($key["attributes"]["birthdate"], -2, 2)
        );
    }
}

function array_sort($array, $on, $order=SORT_ASC){
    $new_array = array();
    $sortable_array = array();

    if (count($array) > 0) {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $k2 => $v2) {
                    if ($k2 == $on) {
                        $sortable_array[$k] = $v2;
                    }
                }
            } else {
                $sortable_array[$k] = $v;
            }
        }

        switch ($order) {
            case SORT_ASC:
                asort($sortable_array);
            break;
            case SORT_DESC:
                arsort($sortable_array);
            break;
        }

        foreach ($sortable_array as $k => $v) {
            $new_array[$k] = $array[$k];
        }
    }

    return $new_array;
}
    // Descomentar esta linea para ver lo que devuelve la URL de la API (sin entrar a POSTMAN)
    //print("<pre>".print_r($result,true)."</pre>");
?>
<!DOCTYPE html>
<html class='no-js' lang='en'>
    <head>
    <meta charset='utf-8'>
    <meta content='IE=edge,chrome=1' http-equiv='X-UA-Compatible'>
    <title>IDS Cumpleaños del mes actual</title>
    <meta content='lab2023' name='author'>
    <meta content='' name='description'>
    <meta content='' name='keywords'>
    <!-- <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" type="text/css" /> -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.1/bootstrap-table.css" rel="stylesheet" type="text/css" />
    <link href="assets/images/favicon.ico" rel="icon" type="image/ico" />
    <link href="//netdna.bootstrapcdn.com/font-awesome/3.2.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    
    
	<style type="text/css">
        /*@import url('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css');*/

        body {
            margin: 10px;
        }

        .naranja1 {
            color: #FFFFFF;
            background-color: #FC7643!important; 
        }

        .naranja2 {
            color: #FFFFFF;
            background-color: #FF9569!important;
        }
        .centrado {
        text-align: center !Important;
        }

        .company-header-avatar{
            width: 50px;
            height: 50px;
            -webkit-border-radius: 60px;
            -webkit-background-clip: padding-box;
            -moz-border-radius: 60px;
            -moz-background-clip: padding;
            border-radius: 60px;
            background-clip: padding-box;
            margin: 7px 0 0 5px;
            float: left;
            background-size: cover;
            background-position: center center;
        }
	</style>
</head>
<body class='login'>
<div class="container-fluid">
    <div class="jumbotron naranja1">
        <h1>Cumpleaños</h1>
        <h3><?php echo $mes_nombre . $hoy->format('Y'); ?> <span class="float-right"><a href="cumples_refresh.php" style="color: white; font-size: 14px;"><i class="fa fa-refresh"></i> Actualizar mes</a></span></h3>
        
    </div>
</div>
    <div class="container-fluid">
        <div class='row'>
            <div class='col-md-12'>
                
                <table class="table" data-toggle="table" data-sort-name="fecha" data-sort-order="asc"  data-pagination="true" data-search="true">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nombre</th>
                            <th>Edad</th>
                            <th>Celular</th>
                            <th data-field="fecha" data-sortable="true">Dia de Nacimiento</th>
                        </tr>
                    </thead>
                <?php 

                foreach (array_sort($lista, 'dia', SORT_ASC) as $registro) { ?>
                    <tr>
                        <td>
                            <div class="company-header-avatar" style="background-image: url(<?php echo $registro["foto"]?>)"></div>

                            <!-- <img src="<?php echo $registro["foto"]?>" width="50" height="50" class="rounded-circle mx-auto d-block"> -->
                        </td>
                        <td><?php echo $registro["nombre"]?></td>
						<td><?php echo $registro["edad"]->format('%y')?></td>
                        <td><?php echo $registro["celular"]?></td>
                     
                        <td><?php echo $registro["dia"]?></td>
                    </tr>
                <?php } ?>

                </table>
            </div>
        </div>
    </div>

    <script src="https://use.fontawesome.com/11abba52b4.js"></script>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-table/1.11.1/bootstrap-table.js"></script>

</body>
</html>