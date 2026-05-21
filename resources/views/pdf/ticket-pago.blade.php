<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ticket de Pago N° {{ $nro_comprobante }}</title>
    <style>
        @page {
            size: letter portrait;
            margin: 8mm 15mm;
        }

        body {
            font-family: Arial, Helvetica, sans-serif;
            color: #000;
            margin: 0;
            padding: 0;
        }

        /* ---- TICKET BOX ---- */
        .ticket {
            border: 1.5px solid #000;
            padding: 12px 20px;
            height: 11.8cm; /* Altura más segura para evitar la segunda página */
            box-sizing: border-box;
            overflow: hidden;
        }

        /* ---- HEADER ---- */
        .header {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            border-bottom: 2px solid #000;
            margin-bottom: 5px;
        }

        .header td {
            vertical-align: middle;
            padding-bottom: 6px;
        }

        .header .col-logo {
            width: 90px; /* Espacio para el logo */
            text-align: left;
        }

        .header .col-empty {
            width: 90px; /* Columna invisible del mismo tamaño para balancear */
        }

        .header .col-logo img {
            width: 80px; /* Logo más grande */
            height: auto;
        }

        .header .col-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
        }

        .header .col-title .sub {
            font-size: 12px;
            font-weight: bold;
            margin-top: 3px;
        }

        /* ---- FECHA TOP ---- */
        .fecha-top {
            text-align: right;
            font-size: 11px;
            margin-bottom: 8px;
            color: #333;
        }

        /* ---- DATA ---- */
        .datos {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-bottom: 12px;
        }

        .datos th,
        .datos td {
            border: 1px solid #000;
            padding: 6px 10px;
            font-size: 12px;
            overflow: hidden;
            vertical-align: middle;
        }

        .datos th {
            text-align: left;
            font-weight: bold;
            background-color: #f2f2f2;
            width: 38%;
        }

        .datos td {
            text-align: right;
            width: 62%;
        }

        .highlight-row th,
        .highlight-row td {
            font-weight: bold;
        }

        /* ---- FOOTER ---- */
        .pie-firmas {
            width: 100%;
            table-layout: fixed;
            border-collapse: collapse;
            margin-top: 25px;
        }

        .pie-firmas td {
            vertical-align: bottom;
            width: 50%;
            text-align: center;
            padding: 0 25px;
        }

        .linea-firma {
            border-bottom: 1px solid #000;
            height: 35px;
            margin-bottom: 5px;
        }

        .lbl-firma {
            font-size: 10px;
            font-weight: bold;
        }

        /* ---- CORTE ---- */
        .cut-line {
            width: 100%;
            border-top: 1px dashed #888;
            margin: 0.4cm 0; /* Espaciado antes y después de la línea */
        }
    </style>
</head>
<body>

    <!-- ================= ORIGINAL ================= -->
    <div class="ticket">
        <table class="header">
            <tr>
                <td class="col-logo"><img src="{{ $logo_path }}" alt="Logo"></td>
                <td class="col-title">
                    RECIBO DE CAJA MORENADA UNITEPC
                    <div class="sub">COMPROBANTE DE PAGO N° {{ $nro_comprobante }} ORIGINAL</div>
                </td>
                <td class="col-empty"></td>
            </tr>
        </table>
        
        <div class="fecha-top">
            Fecha: {{ $fecha_pago }} {{ $hora_pago }}
        </div>

        <table class="datos">
            <tr>
                <th>FRATERNO</th>
                <td>{{ $fraterno_nombre }}</td>
            </tr>
            <tr>
                <th>FESTIVIDAD / GESTIÓN</th>
                <td>{{ $festividad }}</td>
            </tr>
            <tr>
                <th>BLOQUE</th>
                <td>{{ $bloque_nombre }}</td>
            </tr>
            <tr>
                <th>TIPO DE FRATERNO</th>
                <td>{{ $tipo_fraterno }}</td>
            </tr>
            <tr>
                <th>FORMA DE PAGO</th>
                <td>{{ $metodo_pago }}</td>
            </tr>
            <tr>
                <th>MONTO TOTAL ASIGNADO</th>
                <td>Bs. {{ $monto_asignado }}</td>
            </tr>
            <tr>
                <th>ABONOS ANTERIORES</th>
                <td>Bs. {{ $pagos_anteriores }}</td>
            </tr>
            <tr class="highlight-row">
                <th>IMPORTE PAGADO</th>
                <td>Bs. {{ $monto_pagado }}</td>
            </tr>
            <tr class="highlight-row">
                <th>SALDO PENDIENTE RESTANTE</th>
                <td>
                    @if($saldo_pendiente === '0')
                        COMPLETADO
                    @else
                        Bs. {{ $saldo_pendiente }}
                    @endif
                </td>
            </tr>
        </table>

        <table class="pie-firmas">
            <tr>
                <td>
                    <div class="linea-firma"></div>
                    <div class="lbl-firma">ENTREGUÉ CONFORME</div>
                </td>
                <td>
                    <div class="linea-firma"></div>
                    <div class="lbl-firma">RECIBÍ CONFORME</div>
                </td>
            </tr>
        </table>
    </div>

    <!-- ================= CORTE ================= -->
    <div class="cut-line"></div>

    <!-- ================= COPIA ================= -->
    <div class="ticket">
        <table class="header">
            <tr>
                <td class="col-logo"><img src="{{ $logo_path }}" alt="Logo"></td>
                <td class="col-title">
                    RECIBO DE CAJA MORENADA UNITEPC
                    <div class="sub">COMPROBANTE DE PAGO N° {{ $nro_comprobante }} COPIA DE ARCHIVO</div>
                </td>
                <td class="col-empty"></td>
            </tr>
        </table>

        <div class="fecha-top">
            Fecha: {{ $fecha_pago }} {{ $hora_pago }}
        </div>

        <table class="datos">
            <tr>
                <th>FRATERNO</th>
                <td>{{ $fraterno_nombre }}</td>
            </tr>
            <tr>
                <th>FESTIVIDAD / GESTIÓN</th>
                <td>{{ $festividad }}</td>
            </tr>
            <tr>
                <th>BLOQUE</th>
                <td>{{ $bloque_nombre }}</td>
            </tr>
            <tr>
                <th>TIPO DE FRATERNO</th>
                <td>{{ $tipo_fraterno }}</td>
            </tr>
            <tr>
                <th>FORMA DE PAGO</th>
                <td>{{ $metodo_pago }}</td>
            </tr>
            <tr>
                <th>MONTO TOTAL ASIGNADO</th>
                <td>Bs. {{ $monto_asignado }}</td>
            </tr>
            <tr>
                <th>ABONOS ANTERIORES</th>
                <td>Bs. {{ $pagos_anteriores }}</td>
            </tr>
            <tr class="highlight-row">
                <th>IMPORTE PAGADO</th>
                <td>Bs. {{ $monto_pagado }}</td>
            </tr>
            <tr class="highlight-row">
                <th>SALDO PENDIENTE RESTANTE</th>
                <td>
                    @if($saldo_pendiente === '0')
                        COMPLETADO
                    @else
                        Bs. {{ $saldo_pendiente }}
                    @endif
                </td>
            </tr>
        </table>

        <table class="pie-firmas">
            <tr>
                <td>
                    <div class="linea-firma"></div>
                    <div class="lbl-firma">ENTREGUÉ CONFORME</div>
                </td>
                <td>
                    <div class="linea-firma"></div>
                    <div class="lbl-firma">RECIBÍ CONFORME</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
