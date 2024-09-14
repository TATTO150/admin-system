const express = require ('express')
const routes = express.Router()

routes.get('/Areas',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_area' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Areas/:COD_AREA', (req, res) => {
    const { COD_AREA } = req.params; // Asegúrate de que el nombre del parámetro coincida
    const consulta = 'SELECT * FROM tbl_area WHERE COD_AREA = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [COD_AREA], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron Areas con COD_AREA:', COD_AREA);
                return res.status(404).send({ message: 'Area no encontrado' });
            }

            res.send(rows[0]); // Devolver el primer (y único) resultado como objeto
        });
    });
});
routes.post('/INS_AREA',(req, res)=>{
    const {NOM_AREA} = req.body;
    const consulta = `call INS_AREA('${NOM_AREA}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Area Ingresada Correctamente')
                else
                console.log(err)
            })
        })
    })


     //ACTUALIZAR AREAS
     routes.put('/Areas/:COD_AREA', (req, res) => {
        const { COD_AREA } = req.params;
        const { NOM_AREA} = req.body;
    
        const consulta = `
            CALL UPD_AREA(
                ?, ?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                COD_AREA,
                NOM_AREA
            
            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Area Actualizada Correctamente');
            });
        });
    });
    
   //ELIMINAR AREAS
        routes.delete('/Areas/delete/:COD_AREA',(req, res)=>{
            const {COD_AREA} = req.params;
            const consulta = `call ELI_AREA(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [COD_AREA], (err, rows)=>{
                        if(!err)
                        res.send('Areas Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
