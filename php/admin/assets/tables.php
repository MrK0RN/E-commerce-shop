<?php
//$table_name, $add_src, $edit, $delete
include "../system/db.php";
$responce = pgQuery("SELECT * FROM ".$table_name.";");
var_dump($responce);
$text2 = '
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/tables.css">';
 
$text2 .= '
    <!-- Элементы управления таблицей -->
    <div class="table-controls">
        <input type="text" class="search-box" placeholder="Поиск...">
        <a class="add-btn" href="'.$add_src.'">
            <i class="fas fa-plus"></i> Добавить
        </a>
    </div>';
$tableHead = '<!-- Таблица -->
	<table class="data-table">
	<thead>
			<tr>';	
$tableBody = "</tr>
		</thead>
		<tbody>";
$rowEnd = "<td>";

if ($edit == true){
	$rowEnd .= '<button class="action-btn edit-btn">
						<i class="fas fa-edit"></i> Изменить
					</button>';
}
if ($delete == true){
	$rowEnd .= '<button class="action-btn delete-btn">
						<i class="fas fa-trash"></i> Удалить
					</button>';
}
$rowEnd .= "</td>";

	
foreach ($responce[0] as $key => $value) {
	$tableHead.= "<th>".$key."</th>";
}
$tableHead .= "<th>Действия</th>";
foreach ($responce as $unique) {
	$tableBody.= "<tr>";
	foreach ($unique as $key => $value) {
		if ($key == "status"){
			$tableBody.= "<td><span class='status '".$value.">".$value."</span></td>";
		} else {
			$tableBody.= "<td>".$value."</td>";
		}
	}
	$tableBody.= $rowEnd;
	$tableBody.= "</tr>";
}

$tableBody .= "</tbody>
	</table>";

$text2 .= $tableHead;
$text2 .= $tableBody;

?>
