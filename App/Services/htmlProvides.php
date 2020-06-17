<?php

$html = array();

$html['js'] = [
  'toastr' => 'plugins/toastr/build/toastr.min.js',
  'datatables' => [
    //'bower_components/datatables.net/js/jquery.dataTables.min.js',
    //'bower_components/datatables.net-bs/js/dataTables.bootstrap.min.js'
    'plugins/DataTables/datatables.min.js'
  ],
    'chartjs' => [
        'bower_components/chart.js/Chart.js'
    ],
  'fullcalendar' => 'bower_components/fullcalendar/dist/fullcalendar.min.js',

  'inputmask' => [
      'plugins/input-mask/jquery.inputmask.js',
      'plugins/input-mask/jquery.inputmask.date.extensions.js',
      'plugins/input-mask/jquery.inputmask.extensions.js'
      ],

  'jquerymask' => 'plugins/jquery.mask/jquery.mask.min.js',

  'select2' => 'bower_components/select2/dist/js/select2.min.js',

  'daterange' => [
      'bower_components/moment/min/moment.min.js',
      'bower_components/bootstrap-daterangepicker/daterangepicker.js'
    ],

  'datepicker' => 'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',

  'colorpicker' => 'bower_components/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js',

  'time picker' => 'plugins/timepicker/bootstrap-timepicker.min.js',

  'validate' => 'plugins/validate/jquery.validate.min.js',

  'icheck' => 'plugins/iCheck/icheck.min.js',
  
  'bootbox' => 'plugins/bootbox/bootbox.all.js',

  'axios' => 'js/axios.min.js',
];

$html['css'] = [
  'toastr' => 'plugins/toastr/build/toastr.min.css',
  'datatables' => [
      //'bower_components/datatables.net-bs/css/dataTables.bootstrap.min.css',
      'plugins/DataTables/datatables.css'
  ],
  'fullcalendar' => [
    'bower_components/fullcalendar/dist/fullcalendar.css',
    'bower_components/fullcalendar/dist/fullcalendar.print.min.css',
  ],

  'select2' => 'bower_components/select2/dist/css/select2.min.css',

  'datepicker' => 'bower_components/bootstrap-datepicker/dist/css/bootstrap-datepicker.min.css',

  'colorpicker' => 'bower_components/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css',

  'timepicker' => 'plugins/timepicker/bootstrap-timepicker.min.css',

  'icheck' => 'plugins/iCheck/all.css'
];

return $html;