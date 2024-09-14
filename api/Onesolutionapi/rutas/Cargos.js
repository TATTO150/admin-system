const express = require ('express')
const routes = express.Router()

routes.get('/Cargos',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT* FROM tbl_cargos' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Cargos/:COD_CARGO', (req, res) => {
    const { COD_CARGO } = req.params; // Asegúrate de que el nombre del parámetro coincida
    const consulta = 'SELECT * FROM tbl_cargos WHERE COD_CARGO = ?';
    
    req.getConnection((err, conn) => {
        if (err) {
            console.error('Error en la conexión a la base de datos:', err);
            return res.status(500).send(err);
        }

        conn.query(consulta, [COD_CARGO], (err, rows) => {
            if (err) {
                console.error('Error en la consulta SQL:', err);
                return res.status(500).send(err);
            }
            
            if (rows.length === 0) {
                console.log('No se encontraron cargos con COD_CARGO:', COD_CARGO);
                return res.status(404).send({ message: 'Cargo no encontrado' });
            }

            res.send(rows[0]); // Devolver el primer (y único) resultado como objeto
        });
    });
});
routes.post('/INS_CARGOS',(req, res)=>{
    const {NOM_CARGO, SALARIOS, FUNCION_PRINCIPAL} = req.body;
    const consulta = `call INS_CARGOS('${NOM_CARGO}','${SALARIOS}','${FUNCION_PRINCIPAL}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Cagos Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })


     //ACTUALIZAR CARGOS
     routes.put('/Cargos/:COD_CARGO', (req, res) => {
        const { COD_CARGO } = req.params;
        const { NOM_CARGO, SALARIOS, FUNCION_PRINCIPAL} = req.body;
    
        const consulta = `
            CALL UPD_CARGOS(
                ?, ?, ?, ?
            );
        `;
    
        req.getConnection((err, conn) => {
            if (err) {
                console.error('Error en la conexión a la base de datos:', err);
                return res.status(500).send(err);
            }
    
            const parametros = [
                COD_CARGO,
                NOM_CARGO,
                SALARIOS,
                FUNCION_PRINCIPAL
            
            ];
    
            conn.query(consulta, parametros, (err, rows) => {
                if (err) {
                    console.error('Error en la consulta SQL:', err);
                    return res.status(500).send(err);
                }
    
                res.send('Cargos Actualizado Correctamente');
            });
        });
    });
    
   //ELIMINAR CARGO
        routes.delete('/Cargos/delete/:COD_CARGO',(req, res)=>{
            const {COD_CARGO} = req.params;
            const consulta = `call ELI_CARGOS(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [COD_CARGO], (err, rows)=>{
                        if(!err)
                        res.send('Cargos Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
