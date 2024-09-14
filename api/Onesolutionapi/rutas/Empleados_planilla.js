const express = require ('express')
const routes = express.Router()

routes.get('/empleados_planillas',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_empleado_planilla' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/empleados_planillas/:COD_EMPLEADO_PLANILLA', (req, res) => {
    const { COD_EMPLEADO_PLANILLA } = req.params; // Asegúrate de que el nombre del parámetro coincida
    const consulta = 'SELECT * FROM tbl_empleado_planilla WHERE COD_EMPLEADO_PLANILLA = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [COD_EMPLEADO_PLANILLA], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron empleado planillas con COD_EMPLEADO_PLANILLA:',COD_EMPLEADO_PLANILLA);
                return res.status(404).send({ message: 'empleado planillas no encontrado' });
            }

            res.send(rows[0]); // Devolver el primer (y único) resultado como objeto
        });
    });
});
routes.post('/INS_EMPLEADO_PLANILLA',(req, res)=>{
    const {COD_EMPLEADO,COD_PLANILLA, SALARIO_BASE, DEDUCCIONES, SALARIO_NETO} = req.body;
    const consulta = `call INS_EMPLEADO_PLANILLA('${COD_EMPLEADO}','${COD_PLANILLA}','${SALARIO_BASE}','${DEDUCCIONES}','${SALARIO_NETO}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Empleado Planilla Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })


     //ACTUALIZAR USUARIO
     routes.put('/empleados_planillas/:COD_EMPLEADO_PLANILLA', (req, res) => {
        const { COD_EMPLEADO_PLANILLA} = req.params;
        const { COD_EMPLEADO,COD_PLANILLA, SALARIO_BASE, DEDUCCIONES, SALARIO_NETO } = req.body;
    
        const consulta = `
            CALL UPD_EMPLEADO_PLANILLA(
                ?, ?, ?, ?, ?,?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                COD_EMPLEADO_PLANILLA,
                COD_EMPLEADO,
                COD_PLANILLA,
                SALARIO_BASE,
                DEDUCCIONES,
                SALARIO_NETO
            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Empleados Planillas Actualizado Correctamente');
            });
        });
    });
    
   //ELIMINAR USUARIO
        routes.delete('/empleados_planillas/delete/:COD_EMPLEADO_PLANILLA',(req, res)=>{
            const { COD_EMPLEADO_PLANILLA} = req.params;
            const consulta = `call 	ELI_EMPLEADO_PLANILLA(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [COD_EMPLEADO_PLANILLA], (err, rows)=>{
                        if(!err)
                        res.send('Usuarios Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
