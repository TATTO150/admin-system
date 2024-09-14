const express = require ('express')
const routes = express.Router()

routes.get('/Compras',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_compra' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Compras/:COD_COMPRA', (req, res) => {
    const { COD_COMPRA } = req.params; // Asegúrate de que el nombre del parámetro coincida
    const consulta = 'SELECT * FROM tbl_compra WHERE COD_COMPRA = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [COD_COMPRA], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron empleado con COD_COMPRA:', COD_COMPRA);
                return res.status(404).send({ message: 'compra no encontrado' });
            }

            res.send(rows[0]); // Devolver el primer (y único) resultado como objeto
        });
    });
});
routes.post('/INS_COMPRAS',(req, res)=>{
    const {DESC_COMPRA, COD_PROYECTO, FEC_REGISTRO, TIP_COMPRA, PRECIO_VALOR} = req.body;
    const consulta = `call INS_COMPRAS('${DESC_COMPRA}','${COD_PROYECTO}','${FEC_REGISTRO}','${TIP_COMPRA}','${PRECIO_VALOR}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Compra Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })


     //ACTUALIZAR COMPRA
     routes.put('/Compras/:COD_COMPRA', (req, res) => {
        const { COD_COMPRA } = req.params;
        const { DESC_COMPRA, COD_PROYECTO, FEC_REGISTRO, TIP_COMPRA, PRECIO_VALOR} = req.body;
    
        const consulta = `
            CALL UPD_COMPRAS(
                ?, ?, ?, ?, ?, ?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                COD_COMPRA,
                DESC_COMPRA,
                COD_PROYECTO,
                FEC_REGISTRO,
                TIP_COMPRA,
                PRECIO_VALOR
                
                


            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Compra Actualizado Correctamente');
            });
        });
    });
    
   //ELIMINAR COMPRA
        routes.delete('/Compras/delete/:COD_COMPRA',(req, res)=>{
            const { COD_COMPRA} = req.params;
            const consulta = `call ELI_COMPRAS(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [COD_COMPRA], (err, rows)=>{
                        if(!err)
                        res.send('Compra Eliminada Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
