const express = require ('express')
const routes = express.Router()

routes.get('/Parametros',(req,res)=>{
    req.getConnection((err,conn)=>{
        if(err) return res.send(err)
            
            conn.query('SELECT * FROM tbl_ms_parametros' ,(err,rows)=>{
                if(err)return res.send(err)
                    res.send(rows)
            })
    })
})
routes.get('/Parametros/:Id_Parametro',(req,res)=>{
    const {Id_Parametro}=req.params;
    const consulta='SELECT * FROM tbl_ms_parametros where Id_Parametro=?';
    req.getConnection((err,conn)=>{
        conn.query(consulta,[Id_Parametro],(err,rows)=>{
            if(err)return res.send(err)
                res.send(rows)
        })
    })
})
routes.post('/INS_PARAMETROS',(req, res)=>{
    const {Id_Parametro,Parametro, Valor, Id_Usuario, Fecha_Creacion, Fecha_Modificacion} = req.body;
    const consulta = `call INS_PARAMETROS('${Id_Parametro}','${Parametro}','${Valor}','${Id_Usuario}','${Fecha_Creacion}','${Fecha_Modificacion}')`;
    
    req.getConnection((err, conn)=>{
            conn.query(consulta, (err, rows)=>{
                if(!err)
                res.send('Parametros Ingresado Correctamente')
                else
                console.log(err)
            })
        })
    })


    //ACTUALIZAR PARAMETROS
    routes.put('/Parametros/:Id_Parametro',(req, res)=>{
        const { Id_Parametro} = req.params;
        const { Parametro,Valor,Id_Usuario, Fecha_Creacion, Fecha_Modificacion} = req.body;
        const consulta = `call UPD_PARAMETROS(?,'${Parametro}','${Valor}','${Id_Usuario}','${Fecha_Creacion}','${Fecha_Modificacion}')`;
        req.getConnection((err, conn)=>{
                conn.query(consulta, [Id_Parametro], (err, rows)=>{
                    if(!err)
                    res.send('Usuarios Actualizado Correctamente')
                    else
                    console.log(err)
                })
            })
        })

   //ELIMINAR PARAMETROS
        routes.delete('/Parametros/delete/:Id_Parametro',(req, res)=>{
            const { Id_Parametro} = req.params;
            const consulta = `call ELI_PARAMETROS(?)`;
            req.getConnection((err, conn)=>{
                    conn.query(consulta, [Id_Parametro], (err, rows)=>{
                        if(!err)
                        res.send('Parametro Eliminado Correctamente')
                        else
                        console.log(err)
                    })
                })
            }) 

module.exports = routes
