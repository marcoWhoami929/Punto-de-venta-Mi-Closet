<?php

const APP_URL = "http://localhost/pos/";
const APP_NAME = "Ventas Mi Closet";
const APP_SESSION_NAME = "POS";

/*----------  Tipos de documentos  ----------*/
const TIPO_USUARIOS = ["Facebook", "General"];


/*----------  Tipos de unidades de productos  ----------*/
const PRODUCTO_UNIDAD = ["Pieza", "Caja"];

/*----------  Configuración de moneda  ----------*/
const MONEDA_SIMBOLO = "$";
const MONEDA_NOMBRE = "MXN";
const MONEDA_DECIMALES = "2";
const MONEDA_SEPARADOR_MILLAR = ",";
const MONEDA_SEPARADOR_DECIMAL = ".";
const ROUTE_QR = APP_URL . "app/ajax/codes/";


/*----------  Marcador de campos obligatorios (Font Awesome) ----------*/
const CAMPO_OBLIGATORIO = '&nbsp; <i class="fas fa-edit"></i> &nbsp;';

/*----------  Zona horaria  ----------*/
date_default_timezone_set("America/Mexico_City");

	/*
		Configuración de zona horaria de tu país, para más información visita
		http://php.net/manual/es/function.date-default-timezone-set.php
		http://php.net/manual/es/timezones.php
	*/