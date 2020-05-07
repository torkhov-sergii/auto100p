@extends('layout.default')

@section('content')
    <div class="contacts">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="map">
                        <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d1268.6222930984761!2d30.590098109964103!3d50.5110157544217!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNTDCsDMwJzM5LjciTiAzMMKwMzUnMjguNCJF!5e0!3m2!1sen!2sde!4v1588680216862!5m2!1sen!2sde"
                                width="100%" height="450" frameborder="0" allowfullscreen="" aria-hidden="false" tabindex="0">
                        </iframe>
                    </div>
                </div>

                <div class="col-md-4">
                    <h1>
                        Контактная информация
                    </h1>

                    <div class="contacts__list">
                        <div class="contacts__item item-contact">
                            <i class="item-contact__icon fal fa-comment-alt-lines"></i>

                            <div class="item-contact__content">
                                <div class="item-contact__title">
                                    Адрес:
                                </div>
                                <div class="item-contact__description">
                                    г. Киев, Троещина, ул. Пожарского, 2
                                </div>
                            </div>
                        </div>

                        <div class="contacts__item item-contact">
                            <i class="item-contact__icon fal fa-phone"></i>

                            <div class="item-contact__content">
                                <div class="item-contact__title">
                                    Телефон:
                                </div>
                                <div class="item-contact__description">
                                    8 (495) 123-45-67
                                </div>
                            </div>
                        </div>

                        <div class="contacts__item item-contact">
                            <i class="item-contact__icon fal fa-alarm-clock"></i>

                            <div class="item-contact__content">
                                <div class="item-contact__title">
                                    Время работы
                                </div>
                                <div class="item-contact__description">
                                    Понедельник - Пятница: 8:00 - 18:00
                                    <br>Суббота: 8:00 - 15:00
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
