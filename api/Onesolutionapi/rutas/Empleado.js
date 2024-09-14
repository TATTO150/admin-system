const express = require ('express')
const routes = express.Router()

routes.get('/Empleados',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_empleado' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Empleados/:COD_EMPLEADO', (req, res) => {
    const { COD_EMPLEADO } = req.params; // Asegúrate de que el nombre del parámetro coincida
    const consulta = 'SELECT * FROM tbl_empleado WHERE COD_EMPLEADO = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [COD_EMPLEADO], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron empleado con COD_EMPLEADO:', COD_EMPLEADO);
                return res.status(404).send({ message: 'empleado no encontrado' });
            }

            res.send(rows[0]); // Devolver el primer (y único) resultado como objeto
        });
    });
});
routes.post('/INS_EMPLEADO',(req, res)=>{
    const {NOM_EMPLEADO, TIP_EMPLEADO, COD_AREA, DNI_EMPLEADO, LICENCIA_VEHICULAR,COD_CARGO,FEC_INGRESO_EMPLEADO,CORREO_EMPLEADO,DIRECCION_EMPLEADO,CONTRATO_EMPLEADO} = req.body;
    const consulta = `call INS_EMPLEADO('${NOM_EMPLEADO}','${TIP_EMPLEADO}','${COD_AREA}','${DNI_EMPLEADO}','${LICENCIA_VEHICULAR}','${COD_CARGO}','${FEC_INGRESO_EMPLEADO}','${CORREO_EMPLEADO}','${DIRECCION_EMPLEADO}','${CONTRATO_EMPLEADO}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Usuario Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })


     //ACTUALIZAR USUARIO
     routes.put('/Empleados/:COD_EMPLEADO', (req, res) => {
        const { COD_EMPLEADO } = req.params;
        const { NOM_EMPLEADO, TIP_EMPLEADO, COD_AREA, DNI_EMPLEADO, LICENCIA_VEHICULAR,COD_CARGO,FEC_INGRESO_EMPLEADO,CORREO_EMPLEADO,DIRECCION_EMPLEADO,CONTRATO_EMPLEADO } = req.body;
    
        const consulta = `
            CALL UPD_EMPLEADO(
                ?, ?, ?, ?, ?, ?,?,?,?,?,?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                COD_EMPLEADO,
                NOM_EMPLEADO,
                TIP_EMPLEADO,
                COD_AREA,
                DNI_EMPLEADO,
                LICENCIA_VEHICULAR,
                COD_CARGO,
                FEC_INGRESO_EMPLEADO,
                CORREO_EMPLEADO,
                DIRECCION_EMPLEADO,
                CONTRATO_EMPLEADO


            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Empleados Actualizado Correctamente');
            });
        });
    });
    
   //ELIMINAR USUARIO
        routes.delete('/Empleados/delete/:COD_EMPLEADO',(req, res)=>{
            const { COD_EMPLEADO} = req.params;
            const consulta = `call ELI_EMPLEADO(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [COD_EMPLEADO], (err, rows)=>{
                        if(!err)
                        res.send('Usuarios Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
