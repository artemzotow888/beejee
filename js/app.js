$(":input").inputmask();
$("#inputCC").inputmask('9999 9999 9999 9999', { placeholder: '____ ____ ____ ____' });
$("#inputDate").inputmask('dd/mm/yyyy', { placeholder: '__/__/____' });
$("#inputEmail").inputmask({ alias: "email" });
$("#inputIP").inputmask('999.999.999.999', { placeholder: '___.___.___.___' });
$("#inputSK").inputmask('****-****-****-****', { placeholder: '____-____-____-____' });
$("#inputDollar").inputmask('99,99 $', { placeholder: '__,__ $' });
$("#inputTime").inputmask('hh:mm', { placeholder: '__:__ _m', alias: 'time24', hourFormat: '24' });