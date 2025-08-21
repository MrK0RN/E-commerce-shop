<?php
//$table_name, $add_src, $edit, $delete
include "../system/db.php";
$responce = pgQuery("SELECT * FROM ".$table_name.";");
$text2 = '
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/tables.css">';
 
$text2 .= '
    <!-- Элементы управления таблицей -->
    <div class="table-controls">
        <input type="text" class="search-box" placeholder="Поиск...">
        <a class="add-btn" href="assets/forms.php?table_name='.$table_name.'">
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

	
foreach ($responce[0] as $key => $value) {
	$tableHead.= "<th>".$key."</th>";
}
$tableHead .= "<th>Действия</th>";
foreach ($responce as $unique) {
	$tableBody.= "<tr>";
	$cur_id = $unique["id"];

	$rowEnd = "<td>";

	if ($edit == true){
		$rowEnd .= '<button class="action-btn edit-btn" onclick="window.location.href=\'assets/forms.php?table_name=$table_name&edit_id=$cur_id\'">
							<i class="fas fa-edit"></i> Изменить
						</button>';
	}
	if ($delete == true){
		$rowEnd .= '<button class="action-btn delete-btn" onclick="window.location.href=\'assets/delete.php?table_name=$table_name&id=$cur_id\'">
							<i class="fas fa-trash"></i> Удалить
						</button>';
	}
	$rowEnd .= "</td>";

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