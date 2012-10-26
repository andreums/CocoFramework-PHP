<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <title>Hello World</title>
    </head>
    <body onload="printReceipt();">
        <div id="wrapper" style="width: 5.7cm">                      
            <img src="http://www.logoopenstock.com/previews/595-Restaurant-logo-Template.jpg" style="width: 5cm;" />
            <hr/>            
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
            <p>
                <small>Perico de los Palotes Pérez</small><br/>
                <small>CIF B-1234567</small><br/>
                <small>Calle Ficticia 123</small>&nbsp;&nbsp;<small>València</small><br/>
                <small>Tel 961234567</small><br/>
                <small>090002</small>&nbsp;&nbsp;<small>08-sep-12 13:15:10</small>
                
            </p>
            
            <hr/>
            <table border="0">
                <thead>
                    <tr>
                        <th>
                            <small><strong>Concepto</strong></small>
                        </th>
                        <th>
                            <small><strong>Cantidad</strong></small>
                        </th>
                        <th>
                            <small><strong>PVP Uds</strong></small>
                        </th>
                        <th>
                            <small><strong>PVP</strong></small>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Bocadillo Caserillo</td>
                        <td>1</td>
                        <td>5.00</td>
                        <td>5.00</td>
                    </tr>
                    <tr>
                        <td>Bocadillo Chivito</td>
                        <td>2</td>
                        <td>5.00</td>
                        <td>10.0</td>
                    </tr>
                    <tr>
                        <td>Patatas bravas</td>
                        <td>1</td>
                        <td>3.50</td>
                        <td>3.50</td>
                    </tr>
                    <tr>
                        <td>Cañas</td>
                        <td>3</td>
                        <td>3.00</td>
                        <td>3.00</td>
                    </tr>
                </tbody>
            </table>
            <hr/>
            <table>
                <tbody>
                    <tr>                        
                        <td>Subtotal</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td> 
                        <td>&nbsp;</td>                       
                        <td>19.35 €</td>
                    </tr>
                    <tr>
                        <td>IVA (10%)</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td> 
                        <td>&nbsp;</td>
                        <td>2.15 €</td>
                    </tr>
                    <tr>
                        <td>Total con iva</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td>
                        <td>&nbsp;</td> 
                        <td>&nbsp;</td>
                        <td>21.5 €</td>
                    </tr>
                </tbody>
            </table>
            <h4>¡Gracias por su visita!</h4>
        </div>
        
        <script language="javascript">
        <!--
        function printReceipt() {
            window.print();
            alert("¡Imprimiendo!");
            self.close();
        }
        //-->
        </script>
    </body>
</html>