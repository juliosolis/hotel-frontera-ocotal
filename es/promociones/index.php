<?php
require '../../../settings.php';
?>
<!DOCTYPE html>
<!--[if lt IE 7]>
<html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>
<html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>
<html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
    <!-- Global site tag (gtag.js) - Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=UA-151434276-1"></script>
    <script>
        window.dataLayer = window.dataLayer || [];

        function gtag() {
            dataLayer.push(arguments);
        }

        gtag('js', new Date());

        gtag('config', 'UA-151434276-1');
    </script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <title>Promociones | Hotel Frontera en Ocotal Nicaragua</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1"/>
    <link rel="shortcut icon" href="//hotelfronteraocotal.com/es/img/favicon.ico"/>
    <!-- CSS FILES -->
    <link rel="stylesheet" href="/css/bootstrap.min.css">
    <link rel="stylesheet" href="/css/flexslider.css">
    <link rel="stylesheet" href="/css/prettyPhoto.css">
    <link rel="stylesheet" href="/css/main.css">
    <link rel="stylesheet" href="/css/2035.responsive.css">

    <script src="/js/vendor/modernizr-2.8.3-respond-1.1.0.min.js"></script>
    <!-- Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="/js/respond.min.js"></script>
    <![endif]-->
</head>
<body>
<!--[if lt IE 7]>
<p class="chromeframe">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade
    your browser</a> or <a href="http://www.google.com/chromeframe/?redirect=true">activate Google Chrome Frame</a> to
    improve your experience.</p>
<![endif]-->
<div id="wrapper">
    <div class="header"><!-- Header Section -->
        <div class="pre-header"><!-- Pre-header -->
            <div class="container">
                <div class="row">
                    <div class="pull-left pre-address-b"><p><i class="fa fa-map-marker"></i> Contiguo a Gasolinera Ramos
                            <!--, Km. 226 Carretera Panamericana Norte, Ocotal, Nueva Segovia--></p></div>
                    <div class="pull-right">
                        <div class="pull-left">
                            <ul class="pre-link-box">
                                <li><a href="el-hotel.html">El hotel</a></li>
                                <li><a href="contacto.html">Contacto</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="main-header"><!-- Main-header -->
            <div class="container">
                <div class="row">
                    <div class="pull-left">
                        <div class="logo">
                            <a href="index.html"><img alt="Logo" src="/img/logo@2x.png" class="img-responsive"/></a>
                        </div>
                    </div>
                    <div class="pull-right">
                        <div class="pull-left">
                            <nav class="nav">
                                <ul id="navigate" class="sf-menu navigate">
                                    <li><a href="/es/index.html">INICIO</a></li>
                                    <li><a href="/es/el-hotel.html">EL HOTEL</a></li>
                                    <li><a href="/es/habitaciones.html">HABITACIONES</a></li>
                                    <li class="active"><a href="./">PROMOCIONES</a></li>
                                    <li><a href="/es/contacto.html">CONTACTO</a></li>
                                </ul>
                            </nav>
                        </div>
                        <div class="pull-right">
                            <div class="button-style-1 margint45">
                                <a href="contacto.html#" class="nearby_rel"><i class="fa fa-calendar"></i>RESERVAR</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="breadcrumb breadcrumb-1 pos-center">
        <h1>PROMOCIONES</h1>
    </div>
    <div class="content">
        <div class="container">
            <div class="row">
                <?php if ($isAdmin) { ?>
                    <div style="margin: 20px 0 20px 0" class="col-lg-12">
                        <div class="col-lg-4">
                            <button class="btn btn-primary btn-sm" href="#" data-toggle="modal"
                                    data-target="#agregarPromocionModal">Agregar nueva promocion
                            </button>
                            <a class="btn btn-info btn-sm" target="_blank" href="./">Vista previa
                            </a>
                        </div>
                    </div>
                <?php } ?>
                <div class="col-lg-12">
                    <div class="col-lg-4 hide" id="promocion">
                        <div>
                            <h4 class="titulo">This is the greatest offert ever</h4>
                            <div style="text-align: center">
                                <img style="max-width: 100% " src="/img/logo@2x.png" alt="">
                            </div>
                            <p class="desc">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Assumenda delectus
                                deleniti earum esse illo maiores nesciunt quibusdam, reprehenderit sed sequi tempora
                                tenetur vero voluptate? Dolores pariatur quas reiciendis veniam? Laborum.</p>
                            <h5 class="precio">$ 100 </h5>
                        </div>
                        <?php if ($isAdmin) { ?>
                            <div>
                                <div style="margin: 10px 0 10px 0;">
                                    <a class="btnModalEditarPromocion btn btn-primary btn-sm" href="#"
                                       data-toggle="modal" data-target="#editarPromocionModal"
                                       data-promocion="a">Editar</a>
                                    <a class="btn btn-danger btn-sm btnEliminarPromocion" data-promocion="b">
                                        Eliminar
                                    </a>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal -->
    <div class="modal fade" id="agregarPromocionModal" tabindex="-1" role="dialog"
         aria-labelledby="agregarPromocionModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Agregar nueva promocion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="guardarPromocionForm" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="promocion_id" id="promocion_id">
                        <div class="form-group">
                            <label class="form-check-label" for="titulo">Titulo</label>
                            <input type="text" name="titulo" class="form-control" id="titulo" required>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="precio">Precio</label>
                            <input type="text" name="precio" class="form-control" id="precio" required>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="descripcion">Descripción</label>
                            <textarea name="descripcion" class="form-control" id="descripcion" required></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="imagen">Imagen</label>
                            <input type="file" name="imagen" id="imagen">
                        </div>
                        <div class="alert" role="alert"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnGuardarPromocion">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editarPromocionModal" tabindex="-1" role="dialog" aria-labelledby="editarPromocionModal"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar promocion</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="editarPromocionForm" method="post" enctype="multipart/form-data">
                        <input type="hidden" name="id" id="id">
                        <div class="form-group">
                            <label class="form-check-label" for="titulo">Titulo</label>
                            <input type="text" name="titulo" class="form-control" id="editarTitulo"/>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="precio">Precio</label>
                            <input type="text" name="precio" class="form-control" id="editarPrecio">
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="descripcion">Descripción</label>
                            <textarea name="descripcion" class="form-control" id="editarDescripcion"></textarea>
                        </div>
                        <div class="form-group">
                            <label class="form-check-label" for="imagen">Imagen</label>
                            <input type="file" name="imagen" id="editarImagen">
                        </div>
                        <div class="alert" role="alert"></div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" id="btnEditarPromocion">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <div class="footer margint40"><!-- Footer Section -->
        <div class="main-footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-2 col-sm-2 footer-logo">
                        <img alt="Logo" src="/img/logo@2x.png" class="img-responsive">
                    </div>
                    <div class="col-lg-10 col-sm-10">

                        <div class="col-lg-4 col-sm-4">
                            <h6>SOBRE NOSOTROS</h6>
                            <ul class="footer-links">
                                <li><p>El Hotel Frontera ubicado em la ciudad de Ocotal contíguo al Estación Shell Ramos
                                        del Departamento de Nueva Segovia.
                                    <p></li>
                            </ul>
                        </div>


                        <div class="col-lg-4 col-sm-4">
                            <h6>SÍGUENOS</h6>
                            <ul class="footer-links">
                                <li><a target="_blank" href="https://www.facebook.com/HOFROSA">Facebook</a></li>
                                <li><a target="_blank"
                                       href="https://plus.google.com/111318740318485372398/about?gl=ni&hl=es-419">Google
                                        +</a></li>
                                <li><a target="_blank"
                                       href="http://www.tripadvisor.com.mx/Hotel_Review-g1720870-d1718890-Reviews-Hotel_Frontera-Ocotal_Nueva_Segovia_Department.html">Tripadvisor</a>
                                </li>
                            </ul>
                            </ul>
                        </div>


                        <div class="col-lg-4 col-sm-4">
                            <h6>CONTACTO</h6>
                            <ul class="footer-links">
                                <li><p><i class="fa fa-map-marker"></i>Contiguo a Gasolinera Ramos Km. 226 Carretera
                                        Panamericana Norte, Ocotal, Nueva Segovia.</p></li>
                                <li><p><i class="fa fa-phone"></i> +505 2732-2668 / 2732-2669</p></li>
                                <li><p><i class="fa fa-envelope"></i> <a href="mailto:hotelfronterasa@yahoo.com"></a>hotelfronterasa@yahoo.com
                                    </p></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="pre-footer">
            <div class="container">
                <div class="row">
                    <div class="pull-left">
                    <span class="copyright">© 2015 Hotel Frontera -
                        <a href="https://rel.nearbybooking.com/" target="_blank" style="color: #000000">
                            NEARBYBOOKING COMMUNITY</a>
                    </span>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- JS FILES -->
<script src="/js/vendor/jquery-1.11.1.min.js"></script>
<script src="/js/vendor/bootstrap.min.js"></script>
<script src="/js/retina-1.1.0.min.js"></script>
<script src="/js/jquery.flexslider-min.js"></script>
<script src="/js/superfish.pack.1.4.1.js"></script>
<script src="/js/jquery.slicknav.min.js"></script>
<script src="/js/jquery.prettyPhoto.js"></script>
<script src="/js/jquery.parallax-1.1.3.js"></script>
<script src="//maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
<script src="/js/gmaps.js"></script>
<script src="/js/main.js"></script>
<script src="/sendmail/enviarcorreo.js"></script>
<script src="//www.google.com/recaptcha/api.js" async defer></script>

<!-- nearbooking updated -->
<script type="text/javascript">var __nby_opciones = __nby_opciones || {cuenta: 74, idioma: 'es', canal: 1};
    (function (d) {
        var t = d.createElement('script');
        t.type = 'text/javascript';
        t.async = true;
        t.src = 'https://cdn.nearbybooking.com/widget.js';
        var s = d.getElementsByTagName('script')[0];
        s.parentNode.insertBefore(t, s);
    })(document);</script>

<script type="application/javascript">
    $(function () {
        const url = '/api/promociones';

        $.get(url, function (res) {
            for (promocion of res.promociones) {
                let container = $('#promocion').parent(), $promo = $('#promocion').clone();
                $promo.find('h4.titulo').text(promocion.titulo)
                $promo.find('p.desc').text(promocion.descripcion);
                $promo.find('h5.precio').text('$ ' + parseFloat(promocion.precio).toFixed(2));
                $promo.find('img').attr('src', promocion.img);
                $promo.find('.btnModalEditarPromocion').data('promocion', promocion.id)
                $promo.find('.btnEliminarPromocion').data('promocion', promocion.id)
                $promo.appendTo(container).removeClass('hide');
            }
        })

        <?php if ($isAdmin) { ?>
        $('#editarPromocionModal').on('show.bs.modal', function (event) {
            let $invoker = $(event.relatedTarget), promoId = $invoker.data('promocion');
            $.ajax({
                type: 'GET',
                url: url + '/' + promoId,
                dataType: 'JSON',
                success: function (data) {
                    $('#editarPromocionModal input#editarTitulo').val(data.promocion.titulo);
                    $('#editarPromocionModal input#editarPrecio').val(data.promocion.precio);
                    $('#editarPromocionModal textarea#editarDescripcion').val(data.promocion.descripcion);
                    $('#editarPromocionModal input#id').val(data.promocion.id);
                }
            })
        });

        $('#editarPromocionModal').on('hide.bs.modal', function (event) {
            $('#editarPromocionModal input#editarTitulo').val('');
            $('#editarPromocionModal input#editarPrecio').val('');
            $('#editarPromocionModal textarea#editarDescripcion').val('');
            $('#editarPromocionModal textarea#editarImagen').val('');
            $('#editarPromocionModal input#id').val('');
            $('#editarPromocionModal .alert').removeClass('alert-danger').removeClass('alert-success').html('');
        });
        $('#agregarPromocionModal').on('hide.bs.modal', function (event) {
            $('#agregarPromocionModal input#titulo').val('');
            $('#agregarPromocionModal input#precio').val('');
            $('#agregarPromocionModal textarea#descripcion').val('');
            $('#agregarPromocionModal input#imagen').val('');
            $('#agregarPromocionModal input#id').val('');
            $('#agregarPromocionModal .alert').removeClass('alert-danger').removeClass('alert-success').html('');
        });

        $('#btnEditarPromocion').on('click', function () {
            let promoId = $('#editarPromocionModal input#id').val();
            let form = new FormData(document.getElementById("editarPromocionForm"));
            form.append('imagen', $('input[name=imagen]')[0].files[0]);
            $.ajax({
                type: 'POST',
                url: url + '/' + promoId,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form,
                success: function (data) {
                    let className = data.success ? 'alert-success' : 'alert-danger';
                    $('form#editarPromocionForm').find('.alert').addClass(className).html(data.msg);
                }
            })
        });

        $('#btnGuardarPromocion').on('click', function () {
            let form = new FormData(document.getElementById("guardarPromocionForm"));
            form.append('imagen', $('input[name=imagen]')[0].files[0]);
            $.ajax({
                type: 'POST',
                url: url,
                dataType: 'json',
                cache: false,
                contentType: false,
                processData: false,
                data: form,
                success: function (data) {
                    let className = data.success ? 'alert-success' : 'alert-danger';
                    $('form#guardarPromocionForm').find('.alert').addClass(className).html(data.msg);
                }
            });
        });

        $(document).on('click', '.btnEliminarPromocion', function () {
            if (confirm('¿Está seguro de querer eliminar esta promocion?')) {
                let promoId = $(this).data('promocion');
                $.ajax({
                    type: 'DELETE',
                    url: url + '/' + promoId,
                    dataType: 'json',
                    cache: false,
                    data: $('form#deletePromocionForm').serialize(),
                    success: function (data) {
                        console.log(data.msg);
                    }
                })
            }
        });
        <?php } ?>
    });
</script>

</body>
</html>