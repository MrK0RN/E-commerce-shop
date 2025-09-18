<?php

//$responce, $add_src, $edit, $delete
$table_name = "social_networks";
$add = true;
$edit = true;
$delete = true;
include "assets/tables.php";
$main = '<div class="social-reminder">
  <div class="social-reminder__icon">
    <i class="fa-solid fa-triangle-exclamation"></i>
  </div>
  <div class="social-reminder__content">
    <h4 class="social-reminder__title">Требуется настройка социальных сетей</h4>
    <p class="social-reminder__text">Для того чтобы все работало, создайте записи, для этого заполните эти social_network и их link:</p>
    <ul class="social-reminder__list">
      <li class="social-reminder__item">
        <span>meta</span>
      </li>
      <li class="social-reminder__item">
        <span>x</span>
      </li>
      <li class="social-reminder__item">
        <span>instagram</span>
      </li>
      <li class="social-reminder__item">
        <span>linkedin</span>
      </li>
    </ul>
  </div>
</div>

<style>
.social-reminder {
  display: flex;
  align-items: flex-start;
  gap: 15px;
  padding: 20px;
  background: linear-gradient(135deg, #fff8e1 0%, #ffecb3 100%);
  border: 1px solid #ffd54f;
  border-radius: 12px;
  margin: 20px 0;
  box-shadow: 0 4px 12px rgba(255, 179, 0, 0.15);
}

.social-reminder__icon {
  color: #ff9800;
  font-size: 24px;
  margin-top: 2px;
}

.social-reminder__content {
  flex: 1;
}

.social-reminder__title {
  margin: 0 0 12px 0;
  color: #e65100;
  font-size: 18px;
  font-weight: 600;
}

.social-reminder__text {
  margin: 0 0 15px 0;
  color: #5d4037;
  line-height: 1.5;
}

.social-reminder__list {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 12px;
  margin: 0;
  padding: 0;
  list-style: none;
}

.social-reminder__item {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 12px;
  background: rgba(255, 255, 255, 0.8);
  border-radius: 8px;
  border: 1px solid #ffd54f;
}

.social-reminder__item i {
  width: 20px;
  text-align: center;
  color: #ff6d00;
}

.social-reminder__item span {
  color: #5d4037;
  font-size: 14px;
  font-weight: 500;
}

/* Адаптивность */
@media (max-width: 768px) {
  .social-reminder {
    flex-direction: column;
    text-align: center;
  }
  
  .social-reminder__list {
    grid-template-columns: 1fr;
  }
}

@media (max-width: 480px) {
  .social-reminder {
    padding: 15px;
  }
  
  .social-reminder__title {
    font-size: 16px;
  }
}
</style>

<!-- Подключение Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">';
$main .= $text2;
?>
