<style type="text/Css">
<!--
.test1
{
    border: solid 1px #FF0000;
    background: #FFFFFF;
    border-collapse: collapse;
}
-->
</style>
<page style="font-size: 11px">
    <span style="font-weight: bold; font-size: 18pt; color: #FF0000; font-family: Times">Bonjour, voici quelques exemples<br></span>
	<div style="width:500px;">
	<span style="width:50%;text-align:left;">aaaa</span>
	<span style="width:50%;text-align:right;">bbb</span>
	</div>
	<br>
    Barre horizontale &lt;hr&gt;<hr style="height: 4mm; background: #AA5500; border: solid 1mm #0055AA">
    Exemple de lien : <a href="http://html2pdf.fr/" >le site HTML2PDF</a><br>
    <br>
    Image : <img src="./res/logo.gif" alt="Logo" width=150 /><br>
    <br>
    Alignement horizontal des DIVs et TABLEs<br />
    <table style="text-align: center; border: solid 2px red; background: #FFEEEE;width: 40%" align="center"><tr><td style="width: 100%">Test 1</td></tr></table><br />
    <table style="text-align: center; border: solid 2px red; background: #FFEEEE;width: 40%; margin: auto"><tr><td style="width: 100%">Test 2</td></tr></table><br />
    <div style="text-align: center; border: solid 2px red; background: #FFEEEE;width: 40%; margin: auto">Test 3</div><br />
    test de tableau imbriqué :<br>
    <table style="width:100%;" border="1" bordercolor="#007" bgcolor="#AAAAAA" align="center">
		<tr > 
			<td width="50%"> aaaa</td>
			<td width="50%"> aaaa</td>
		</tr>
    </table>
    <br>
    Exemple avec border et padding : <br>
    <table style="border: solid 5mm #770000; padding: 5mm;" cellspacing="0" >
        <tr>
            <td style="border: solid 3mm #007700; padding: 2mm;"><img src="./res/off.png" alt="" style="width: 20mm"></td>
        </tr>
    </table>
    <img src="./res/off.png" style="width: 10mm;"><img src="./res/off.png" style="width: 10mm;"><img src="./res/off.png" style="width: 10mm;"><img src="./res/off.png" style="width: 10mm;"><img src="./res/off.png" style="width: 10mm;"><br>
    <br>
    <table style="border: solid 1px #440000; width: 150px"  cellspacing="0"><tr><td style="width: 100%">Largeur : 150px</td></tr></table><br>
    <table style="border: solid 1px #440000; width: 150pt"  cellspacing="0"><tr><td style="width: 100%">Largeur : 150pt</td></tr></table><br>
    <table style="border: solid 1px #440000; width: 100mm"  cellspacing="0"><tr><td style="width: 100%">Largeur : 100mm</td></tr></table><br>
    <table style="border: solid 1px #440000; width: 5in"    cellspacing="0"><tr><td style="width: 100%">Largeur : 5in</td></tr></table><br>
    <table style="border: solid 1px #440000; width: 100%; float:left;"    cellspacing="0">
		<tr>
			 <td style="width: 30%; text-align: left; border: solid 1px #55DD44">
                    test de texte assez long pour engendrer des retours à la ligne automatique...
                    a b c d e f g h i j k l m n o p q r s t u v w x y z
                </td>
                <td style="width: 70%; text-align: left; border: solid 1px #55DD44;vertical-align:top;">
                    test de texte assez long pour engendrer des retours à la ligne automatique...
                    a b c d e f g h i j k l m n o p q r s t u v w x y z
                    a b c d e f g h i j k l m n o p q r s t u v w x y z

                </td>
		</tr>
	</table><br>
</page>