const express = require ('express')
const routes = express.Router()

routes.get('/planillas',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_planilla' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/planillas/:COD_PLANILLA',(req,res)=>{
    const {COD_PLANILLA}=req.params;
    const consulta='SELECT * FROM tbl_planilla where COD_PLANILLA=?';
    req.getConnection((err,conn)=>{
        conn.query(consulta,[COD_PLANILLA],(err,rows)=>{
            if(err)return res.send(err)
                res.send(rows)
        })
    })
})

//AGREGAR
routes.post('/INS_PLANILLA',(req, res)=>{
    const {FECHA_PAGO,TOTAL_PAGADO} = req.body;
    const consulta = `call INS_PLANILLA('${FECHA_PAGO}','${TOTAL_PAGADO}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Datos ingresados correctamente')
                else
                console.log(err)
            })
        })
    })
    routes.put('/Planillas/:COD_PLANILLA', (req, res) => {
        const { COD_PLANILLA } = req.params;
        const {FECHA_PAGO,TOTAL_PAGADO} = req.body;
    
        const consulta = `
            CALL UPD_PLANILLAS(
                ?, ?, ?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                COD_PLANILLA,
                FECHA_PAGO,
                TOTAL_PAGADO 
            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Planillas Actualizado Correctamente');
            });
        });
    });

    //ELIMINAR PLANILLAS
    routes.delete('/planillas/delete/:COD_PLANILLA',(req, res)=>{
        const { COD_PLANILLA} = req.params;
        const consulta = `call ELI_PLANILLA(?)`;
        req.getConnection((err, conn)=>{
                conn.query(consulta, [COD_PLANILLA], (err, rows)=>{
                    if(!err)
                    res.send('Datos eliminados correctamente')
                    else
                    console.log(err)
                })
            })
        }) 

module.exports = routes
