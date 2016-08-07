
<form id="footer-callback" class="form-vertical form-bordered flexible js-form" action="/api/form/request/" method="post" data-yandex-target="Zvonok">

  <div class="form-header">Возникли вопросы?</div>
  <div class="form-caption">
    Закажите обратный звонок, наш менеджер свяжется с вами в ближайшее время.
  </div>
  <div class="form-field">
    <input type="text" name="name" class="form-input-name" required placeholder="Ваше имя"> </div>

  <div class="form-field">
    <input type="text" name="phone" class="form-input-phone" required placeholder="Телефон"> </div>

  <div class="form-field buttons-row center">
    <input class="btn" name="some_name" value="Заказать обратный звонок" type="submit"> </div>

  <div class="form-result-error"><!-- сообщение и место вывода ошибки --></div>

	<div class="hidden">
    <input type="hidden" value="Заказать обратный звонок" name="form_id" class="form-input-idform">
    <input type="hidden" value="" name="object" class="form-input-object">
    <div class="form-result-success">
      Спасибо! Наш менеджер свяжется с Вами в течение 15 минут.
    </div>
  </div>

</form>