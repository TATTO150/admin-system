const express = require ('express')
const routes = express.Router()

routes.get('/Objeto',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT * FROM tbl_objeto' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Objeto/:Id_Objetos',(req,res)=>{
    const {Id_Objetos}=req.params;
    const consulta='SELECT * FROM tbl_Objeto where Id_Objetos=?';
    req.getConnection((err,conn)=>{
        conn.query(consulta,[Id_Objetos],(err,rows)=>{
            if(err)return res.send(err)
                res.send(rows)
        })
    })
})
routes.post('/INS_OBJETO',(req, res)=>{
    const {Id_Objeto, Objeto, Descripcion, Tipo_Objeto} = req.body;
    const consulta = `call INS_OBJETO('${Id_Objeto}','${Objeto}','${Descripcion}','${Tipo_Objeto}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('objeto Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })


    //ACTUALIZAR OBJETO
    routes.put('/Objeto/:Id_Objeto',(req, res)=>{
        const { Id_Objeto} = req.params;
        const { Objeto,Descripcion, Tipo_Objeto} = req.body;
        const consulta = `call UPD_OBJETO(?,'${Objeto}','${Descripcion}','${Tipo_Objeto}')`;
        req.getConnection((err, conn)=>{
                conn.query(consulta, [Id_Objeto], (err, rows)=>{
                    if(!err)
                    res.send('Objeto Actualizado Correctamente')
                    else
                    console.log(err)
                })
            })
        })

   //ELIMINAR OBJETO
        routes.delete('/Objeto/delete/:Id_Objetos',(req, res)=>{
            const { Id_Objetos} = req.params;
            const consulta = `call ELI_OBJETO(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [Id_Objetos], (err, rows)=>{
                        if(!err)
                        res.send('objeto Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
