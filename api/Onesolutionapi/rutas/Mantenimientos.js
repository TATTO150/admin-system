const express = require ('express')
const routes = express.Router()

routes.get('/Mantenimientos',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_mantenimiento' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Mantenimientos/:COD_MANTENIMIENTO', (req, res) => {
    const { COD_MANTENIMIENTO } = req.params; // Asegúrate de que el nombre del parámetro coincida
    const consulta = 'SELECT * FROM tbl_mantenimiento WHERE COD_MANTENIMIENTO = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [COD_MANTENIMIENTO], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron mantenimiento con COD_MANTENIMIENTO:', COD_MANTENIMIENTO);
                return res.status(404).send({ message: 'mantenimiento no encontrado' });
            }

            res.send(rows[0]); // Devolver el primer (y único) resultado como objeto
        });
    });
});
routes.post('/INS_MANTENIMIENTO',(req, res)=>{
    const {COD_EMPLEADO, COD_ESTADO_MANTENIMIENTO, COD_EQUIPO, DESC_MANTENIMIENTO, FEC_INGRESO,FEC_FINAL_PLANIFICADA,FEC_FINAL_REAL} = req.body;
    const consulta = `call INS_MANTENIMIENTO('${COD_EMPLEADO}','${COD_ESTADO_MANTENIMIENTO}','${COD_EQUIPO}','${DESC_MANTENIMIENTO}','${FEC_INGRESO}','${FEC_FINAL_PLANIFICADA}','${FEC_FINAL_REAL}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Mantenimiento Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })


     //ACTUALIZAR MANTENIMIENTO
     routes.put('/Mantenimientos/:COD_MANTENIMIENTO', (req, res) => {
        const { COD_MANTENIMIENTO } = req.params;
        const {COD_EMPLEADO ,COD_ESTADO_MANTENIMIENTO , COD_EQUIPO , DESC_MANTENIMIENTO , FEC_INGRESO , FEC_FINAL_PLANIFICADA , FEC_FINAL_REAL } = req.body;
    
        const consulta = `
            CALL UPD_MANTENIMIENTO(
                ?, ?, ?, ?, ?, ?,?,?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                COD_MANTENIMIENTO,
                COD_EMPLEADO,
                COD_ESTADO_MANTENIMIENTO,
                COD_EQUIPO,
                DESC_MANTENIMIENTO,
                FEC_INGRESO,
                FEC_FINAL_PLANIFICADA,
                FEC_FINAL_REAL

            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Mantenimiento Actualizado Correctamente');
            });
        });
    });
    
   //ELIMINAR MANTENIMIENTO
        routes.delete('/Mantenimientos/delete/:COD_MANTENIMIENTO',(req, res)=>{
            const { COD_MANTENIMIENTO} = req.params;
            const consulta = `call ELI_MANTENIMIENTO(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [COD_MANTENIMIENTO], (err, rows)=>{
                        if(!err)
                        res.send('Mantenimiento Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes

