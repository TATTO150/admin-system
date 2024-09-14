const express = require ('express')
const routes = express.Router()

routes.get('/Gastos',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_gastos' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Gastos/:COD_GASTO', (req, res) => {
    const { COD_GASTO } = req.params; // Asegúrate de que el nombre del parámetro coincida
    const consulta = 'SELECT * FROM tbl_gastos WHERE COD_GASTO = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [COD_GASTO], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron gastos con COD_GASTO:', COD_GASTO);
                return res.status(404).send({ message: 'Gasto no encontrado' });
            }

            res.send(rows[0]); // Devolver el primer (y único) resultado como objeto
        });
    });
});
routes.post('/INS_GASTOS',(req, res)=>{
    const {COD_COMPRA, COD_PROYECTO, FEC_REGISTRO,SUBTOTAL,TOTAL} = req.body;
    const consulta = `call INS_GASTOS('${COD_COMPRA}','${COD_PROYECTO}','${FEC_REGISTRO}','${SUBTOTAL}','${TOTAL}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Gasto Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })


     //ACTUALIZAR GASTOS
     routes.put('/Gastos/:COD_GASTO', (req, res) => {
        const { COD_GASTO } = req.params;
        const { DESC_GASTO, IMPUESTO_GASTO, TIP_GASTO, FEC_REGISTRO} = req.body;
    
        const consulta = `
            CALL UPD_GASTOS(
                ?, ?, ?, ?, ?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                COD_GASTO,
                DESC_GASTO,
                IMPUESTO_GASTO,
                TIP_GASTO,
                FEC_REGISTRO
                
            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Gastos Actualizado Correctamente');
            });
        });
    });
    
   //ELIMINAR GASTOS
        routes.delete('/Gastos/delete/:COD_GASTO',(req, res)=>{
            const { COD_GASTO } = req.params;
            const consulta = `call ELI_GASTOS(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [COD_GASTO], (err, rows)=>{
                        if(!err)
                        res.send('Gastos Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
