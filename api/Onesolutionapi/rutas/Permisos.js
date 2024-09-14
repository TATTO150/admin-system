const express = require ('express')
const routes = express.Router()

routes.get('/Permisos',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT * FROM tbl_permisos' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Permisos/:COD_PERMISOS', (req, res) => {
    const { COD_PERMISOS } = req.params;
    const consulta = 'SELECT * FROM tbl_permisos WHERE COD_PERMISOS = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [COD_PERMISOS], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron permiso con COD_PERMISOS :', COD_PERMISOS);
                return res.status(404).send({ message: 'Permiso no encontrado' });
            }

            res.send(rows); // Devolver el array completo
        });
    });
});
routes.post('/INS_PERMISOS',(req, res)=>{
    const {Id_Rol, Id_Objeto, Permiso_Insercion, Permiso_Eliminacion, Permiso_Actualizacion,Permiso_Consultar} = req.body;
    const consulta = `call INS_PERMISOS('${Id_Rol}','${Id_Objeto}','${Permiso_Insercion}','${Permiso_Eliminacion}','${Permiso_Actualizacion}','${Permiso_Consultar}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('permiso Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })


     //ACTUALIZAR PERMISOS
     routes.put('/Permisos/:COD_PERMISOS', (req, res) => {
        const {COD_PERMISOS} = req.params;
        const {Id_Rol ,Id_Objeto , Permiso_Insercion , Permiso_Eliminacion , Permiso_Actualizacion , Permiso_Consultar} = req.body;
    
        const consulta = `
            CALL UPD_PERMISOS(
                ?, ?, ?, ?, ?, ?,?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                COD_PERMISOS,
                Id_Rol,
                Id_Objeto,
                Permiso_Insercion,
                Permiso_Eliminacion,
                Permiso_Actualizacion,
                Permiso_Consultar

            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Permiso Actualizado Correctamente');
            });
        });
    });
    
   //ELIMINAR PERMISOS
        routes.delete('/Permisos/delete/:COD_PERMISOS ',(req, res)=>{
            const { COD_PERMISOS } = req.params;
            const consulta = `call 	ELI_PERMISOS(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [COD_PERMISOS ], (err, rows)=>{
                        if(!err)
                        res.send('Permisos Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes


