<div id="sidebar">
    <div class="list-group panel">
        <a href="_login_check.php" class="list-group-item active" align="center"><span class="glyphicon glyphicon-home"></span> หน้าหลัก</a>
        <a href="show.php" class="list-group-item"><span class="glyphicon glyphicon-search"></span> สืบค้น(ซื้อปกติ)</a>
        <a href="show_urgent.php" class="list-group-item"><span class="glyphicon glyphicon-search"></span> สืบค้น(ซื้อเร่งด่วน)</a>
        <?php if ($_SESSION['status'] == "KEY") { ?>
            <a href="#addDropdown" class="list-group-item list-group-item-default" data-toggle="collapse" data-parent="#sidebar"><span class="glyphicon glyphicon-triangle-bottom"></span> เพิ่มใบสั่งซื้อ</a>
            <div class="collapse" id="addDropdown">
                <a href="add.php" class="list-group-item list-group-item-success">&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-minus"></span> ปกติ</a>
                <a href="add_stock.php" class="list-group-item list-group-item-info">&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-minus"></span> เบิกคลัง</a>
                <a href="add_urgent.php" class="list-group-item list-group-item-warning">&nbsp;&nbsp;&nbsp;&nbsp;<span class="glyphicon glyphicon-minus"></span> เร่งด่วน</a>
            </div>

            <a href="key_item.php" class="list-group-item" target="_blank"><span class="glyphicon glyphicon-list"></span> จัดการรายชื่อเครื่องมือฯ ทั้งหมด</a>
        <?php } ?>
        <a href="period_report.php" class="list-group-item"><span class="glyphicon glyphicon-file"></span> รายงาน</a>
        <!--
    <div class="list-group-item">กระดานข่าว:<br/>
        ADMIN
    </div> -->
    </div>   
</div>