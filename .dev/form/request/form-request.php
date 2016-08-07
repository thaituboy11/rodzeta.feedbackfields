
<form class="form-vertical flexible js-form" action="/api/form/request/" method="post">
  <div class="form-header">Отправить заявку</div>
  <div class="form-descr">
Расскажем подробно и покажем дом изнутри.
  </div>

  <!--div class="form-field">
    <input placeholder="Ваше имя *" type="text" name="name" class="form-input-name" required>
  </div-->
  <div class="form-field">
    <input placeholder="Телефон *" type="text" name="phone" class="form-input-phone" required>
  </div>
  <!--div class="form-field">
    <input placeholder="Эл. почта *" type="text" name="email" class="form-input-email" required>
  </div-->

  <?php /* captcha example
  <div class="form-field">
    <div class="captcha-image"></div>
    <a href="#" onclick="document.getElementById('captcha').src = '/securimage/securimage_show.php?' + Math.random(); return false">Обновить</a>
    <input type="text" name="captcha_code" size="10" maxlength="6" />
  </div>
  */ ?>

  <div class="form-result-error"><!-- сообщение и место вывода ошибки --></div>

  <div class="hidden">
    <input type="hidden" value="" name="form_id" class="form-input-idform">
    <input type="hidden" value="" name="object" class="form-input-object">
    <div class="form-result-success">
      Спасибо! Наш менеджер свяжется с Вами в течение 15 минут.
    </div>
  </div>

  <div class="form-field buttons-row center">
    <input class="btn" value="Отправить заявку" type="submit">
  </div>

  <div class="form-field form-footer">
    ...
  </div>


</form>
