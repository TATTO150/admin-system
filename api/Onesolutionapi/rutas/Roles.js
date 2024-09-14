const express = require ('express')
const routes = express.Router()

routes.get('/Roles',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT * FROM tbl_ms_roles' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Roles/:Id_Rol', (req, res) => {
    const { Id_Rol } = req.params; // Asegúrate de que el nombre del parámetro coincida
    const consulta = 'SELECT * FROM tbl_ms_roles WHERE Id_Rol = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [Id_Rol], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron empleado con Id_Rol:', Id_Rol);
                return res.status(404).send({ message: 'Rol no encontrado' });
            }

            res.send(rows[0]); // Devolver el primer (y único) resultado como objeto
        });
    });
});
routes.post('/INS_ROL',(req, res)=>{
    const {Rol,Descripcion} = req.body;
    const consulta = `call INS_ROL('${Rol}','${Descripcion}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Rol Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })

    //ACTUALIZAR ROLES
    routes.put('/Roles/:Id_Rol',(req, res)=>{
        const { Id_Rol} = req.params;
        const { Rol,Descripcion} = req.body;
        const consulta = `call UPD_ROL(?,'${Rol}','${Descripcion}')`;
        req.getConnection((err, conn)=>{
                conn.query(consulta, [Id_Rol], (err, rows)=>{
                    if(!err)
                    res.send('ROL Actualizado Correctamente')
                    else
                    console.log(err)
                })
            })
        })

   //ELIMINAR ROLES
        routes.delete('/Roles/delete/:Id_Rol',(req, res)=>{
            const { Id_Rol} = req.params;
            const consulta = `call ELI_ROL(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [Id_Rol], (err, rows)=>{
                        if(!err)
                        res.send('Rol Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
