const express = require ('express')
const routes = express.Router()

routes.get('/Usuarios',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_ms_usuario' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Usuarios/:Id_usuario', (req, res) => {
    const { Id_usuario } = req.params; // Asegúrate de que el nombre del parámetro coincida
    const consulta = 'SELECT * FROM tbl_ms_usuario WHERE Id_usuario = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [Id_usuario], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron usuarios con Id_usuario:', Id_usuario);
                return res.status(404).send({ message: 'Usuario no encontrado' });
            }

            res.send(rows[0]); // Devolver el primer (y único) resultado como objeto
        });
    });
});
routes.post('/INS_USUARIOS', (req, res) => {
    const { Usuario, Nombre_Usuario, Estado_Usuario, Id_Rol, Fecha_Ultima_Conexion, Fecha_Vencimiento, Correo_Electronico ,Contrasena} = req.body;
    const consulta = `
        CALL INS_USUARIOS(
            '${Usuario}', '${Nombre_Usuario}', '${Estado_Usuario}', '${Id_Rol}',
            '${Fecha_Ultima_Conexion}', '${Fecha_Vencimiento}', '${Correo_Electronico}', '${Contrasena}'
        )
    `;
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, (err, results) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }

            // Asegúrate de que la respuesta contiene el ID
            if (results[0] && results[0][0]) {
                const newUserId = results[0][0].Id_usuario;
                res.json({ Id_usuario: newUserId });
            } else {
                res.status(500).json({ error: 'No se pudo obtener el ID del usuario' });
            }
        });
    });
});

     //ACTUALIZAR USUARIO
     routes.put('/Usuarios/:Id_usuario', (req, res) => {
        const { Id_usuario } = req.params;
        const { Usuario, Nombre_Usuario, Estado_Usuario, Id_Rol, Fecha_Ultima_Conexion, Fecha_Vencimiento, Correo_Electronico } = req.body;
    
        const consulta = `
            CALL UPD_USUARIOS(
                ?, ?, ?, ?, ?, ?,?,?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                Id_usuario,
                Usuario,
                Nombre_Usuario,
                Estado_Usuario,
                Id_Rol,
                Fecha_Ultima_Conexion,
                Fecha_Vencimiento,
                Correo_Electronico
            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Usuarios Actualizado Correctamente');
            });
        });
    });
    
   //ELIMINAR USUARIO
        routes.delete('/Usuarios/delete/:Id_usuario',(req, res)=>{
            const { Id_usuario} = req.params;
            const consulta = `call ELI_USUARIOS(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [Id_usuario], (err, rows)=>{
                        if(!err)
                        res.send('Usuarios Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
