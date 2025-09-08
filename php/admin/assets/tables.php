<?php
//$table_name, $add_src, $edit, $delete
include "../system/db.php";
$responce = pgQuery("SELECT * FROM ".$table_name.";");
$ver = time();
$text2 = '
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/tables.css?v='.$ver.'">';
 
if ($add){
	$text2 .= '
    <!-- Элементы управления таблицей -->
    <div class="table-controls">
        <input type="text" class="search-box" placeholder="Поиск...">
        <a class="add-btn" href="assets/forms.php?table_name='.$table_name.'">
            <i class="fas fa-plus"></i> Добавить
        </a>
    </div>';
}

$tableHead = '<!-- Таблица -->
	<table class="data-table">
	<thead>
			<tr>';	
$tableBody = "</tr>
		</thead>
		<tbody>";

	
if (empty($responce)) {
    // Нет записей — строим заголовки по колонкам
    $columns = pgQuery("SELECT column_name FROM information_schema.columns WHERE table_name = '$table_name';");
    $tableHead = '<!-- Таблица -->
    <table class="data-table">
    <thead>
        <tr>';
    foreach ($columns as $row) {
        $tableHead .= "<th>".$row["column_name"]."</th>";
    }
    $tableHead .= "<th>Действия</th></tr></thead><tbody>";
    $tableBody = '<tr><td colspan="'.(count($columns)+1).'" style="text-align:center;">Нет данных</td></tr>';
    $tableBody .= '</tbody></table>';
    $text2 .= $tableHead . $tableBody;
} else {
	foreach ($responce[0] as $key => $value) {
		$tableHead.= "<th>".$key."</th>";
	}
	$tableHead .= "<th>Действия</th>";
	foreach ($responce as $unique) {
		$tableBody.= "<tr>";
		$cur_id = $unique["id"];

		$rowEnd = "<td>";

		if ($edit == true){
			$rowEnd .= '<button class="action-btn edit-btn" onclick="window.location.href=\'assets/forms.php?table_name='.$table_name.'&edit_id='.$cur_id.'\'">
								<i class="fas fa-edit"></i> Изменить
							</button>';
		}
		if ($delete == true){
			$rowEnd .= '<button class="action-btn delete-btn" onclick="window.location.href=\'assets/delete.php?table_name='.$table_name.'&id='.$cur_id.'\'">
								<i class="fas fa-trash"></i> Удалить
							</button>';
		}
		if (isset($buttons)){
			foreach ($buttons as $key => $value) {
				$rowEnd .= '<button class="action-btn edit-btn" onclick="window.location.href=\''.$value.'?id='.$cur_id.'\'">
								<i class="fas"></i> '.$key.'
							</button>';
			}
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
}
?>