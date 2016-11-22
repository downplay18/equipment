<?php

/* array('ชื่อหัวตาราง', 'ชื่อในฐานข้อมูล', 'ชื่อตัวแปร') */
/* array('user header', 'sql header', '$_POST header') */

$item_headerInfo = array(
    array('ZPO', 'zpo', 'var_zpo'), /* การเรียกใช้ array([0][0], [0][1], [0][2]) */
    array('วันที่ใบเสร็จ', 'slip_date', 'var_slipDate'),
    array('ZDIR', 'zdir', 'var_zdir'),
    array('รายการ', 'detail', 'var_detail'),
    array('หน่วย', 'slip_suffix', 'var_slipSuffix'),
    array('จำนวน', 'qty', 'var_qty'),
    array('ราคาหน่วยละ', 'unit_price', 'var_unitPrice'),
    array('จำนวนเงิน', 'amount', 'var_amount'),
    array('ยอดรวม', 'sub_total', 'var_subTotal'), /* ราคารวม ก่อนคิด VAT7% */
    array('รวมสุทธิ', 'grand_total', 'var_grandTotal'), /* ราคารวม หลังคิด VAT7% */
    array('ผู้เพิ่มรายการ', 'adder', 'var_adder'),
);
$item_size = count($item_headerInfo); /* เท่ากับขนาดของ $header_info */
?>