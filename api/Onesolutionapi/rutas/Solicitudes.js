const express = require ('express')
const routes = express.Router()

routes.get('/Solicitudes',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_solicitudes' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Solicitudes/:COD_SOLICITUD', (req, res) => {
    const { COD_SOLICITUD } = req.params; // Asegúrate de que el nombre del parámetro coincida
    const consulta = 'SELECT * FROM tbl_solicitudes WHERE COD_SOLICITUD = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [COD_SOLICITUD], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron Solucitudes con COD_SOLICITUD:', COD_SOLICITUD);
                return res.status(404).send({ message: 'solicitudes no encontrado' });
            }

            res.send(rows[0]); // Devolver el primer (y único) resultado como objeto
        });
    });
});
routes.post('/INS_SOLICITUDES',(req, res)=>{
    const {COD_EMPLEADO,DESC_SOLICITUD,COD_AREA,COD_PROYECTO,ESTADO_SOLICITUD,PRESUPUESTO_SOLICITUD} = req.body;
    const consulta = `call INS_SOLICITUDES('${COD_EMPLEADO}','${DESC_SOLICITUD}','${COD_AREA}','${COD_PROYECTO}','${ESTADO_SOLICITUD}','${PRESUPUESTO_SOLICITUD}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Usuario Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })
    //ACTUALIZAR SOLICITUD
    routes.put('/Solicitudes/:COD_SOLICITUD', (req, res) => {
        const { COD_SOLICITUD} = req.params;
        const {COD_EMPLEADO,DESC_SOLICITUD,COD_AREA,COD_PROYECTO,ESTADO_SOLICITUD,PRESUPUESTO_SOLICITUD } = req.body;
    
        const consulta = `
            CALL UPD_SOLICITUDES(
                ?, ?, ?, ?, ?,?,?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                COD_SOLICITUD,
                COD_EMPLEADO,
                DESC_SOLICITUD,
                COD_AREA,
                COD_PROYECTO,
                ESTADO_SOLICITUD,
                PRESUPUESTO_SOLICITUD 
            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Solicitud Actualizado Correctamente');
            });
        });
    });
    
   //ELIMINAR SOLICITUD
        routes.delete('/Solicitudes/delete/:COD_SOLICITUD',(req, res)=>{
            const { COD_SOLICITUD} = req.params;
            const consulta = `call ELI_PROYECTOS(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [COD_SOLICITUD], (err, rows)=>{
                        if(!err)
                        res.send('Solicitud Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
