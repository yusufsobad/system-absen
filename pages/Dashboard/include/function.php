<?php

function style_punishment(){
	?>
		table.sobad-punishment{
			padding-left:25px;
			padding-right:25px;
		}

		table.sobad-punishment thead tr{
			text-align:center;
		}

		table.sobad-punishment thead tr{
			background-color:#a4a4ff;
		}

		table.sobad-punishment thead tr th{
			font-size:18px;
		}

		table.sobad-punishment tbody tr{
			background-color:#c2c2fb;
		}

		table.sobad-punishment tbody tr.danger{
			background-color:#ff1b1b;
		}

		table.sobad-punishment tbody tr.warning{
			background-color:#ffc310;
		}

		table.sobad-punishment thead tr th, table.sobad-punishment tbody tr td {
    		padding: 3px;
    		text-align:center;
		}

		ol li{
			list-style-type: decimal;
		}

		ul li{
			list-style-type: lower-alpha;
			padding-left:5px;
		}
	<?php
}