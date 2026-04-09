//Pendiente a desarrollar competencias
let point_visit = {};
console.log("Visita de venta ", visit);

//estructura de visita en el punto
//estructura de visita
// -- Helper

let aux_visit = {
  ...visit,
  visit: {
    ...visit.visit,
    fecha_agendamiento: null,
    geo_position: null,
    id_jornada: visit.id_jornada ? visit.id_jornada : null,
    id_motivo_no_atencion: null,
    orden_ruta: null,
    cleaning_visit_id: null,
  },
};

// Insertar Visita general
if (aux_visit.visit.visit_type === "p" || aux_visit.visit.visit_type === "h") {
  aux_visit.id = await insertVisitDraught.trigger({
    additionalScope: {
      visit: aux_visit.visit,
    },
    onSuccess: function (data) {
      console.log("VISITA GENERAL: " + data.id[0]);
      aux_visit.processed = "yes";
      aux_visit.idVisit = data.id[0];
      aux_visit.error = null;
      updateVisitLocal.trigger({
        additionalScope: {
          visit: aux_visit,
        },
      });
    },
    onFailure: function (error) {
      aux_visit.error = error;
      updateVisitLocal.trigger({
        additionalScope: {
          visit: aux_visit,
        },
      });
    },
  });
}
// Insertando visita de prospección y sus datos
if (aux_visit.visit?.additional_observations?.want_service) {
  console.log("Visita DE PROSPECCION", visit);
 await insertDraughtProspVisit.trigger({
    additionalScope: {
      visit: {
        ...aux_visit.visit,
        general_visit_id: aux_visit.idVisit,
        user_id: aux_visit.visit.id_realizada_por,
        client_id: aux_visit.visit.id_cliente_visitado,
      },
    },
    onSuccess: function (data) {
      aux_visit.idProspection =
        aux_visit.idProspection != null
          ? aux_visit.idProspection + " " + data.id[0].toString()
          : data.id[0].toString();
      updateVisitLocal.trigger({
        additionalScope: {
          visit: aux_visit,
        },
      });
    },
  });
}

//Insertando visita genérica
if (aux_visit.visit.visit_type === "v") {
  // Obteniendo id de los materiales solicitados
  const m_id = aux_visit?.visit?.merchandising_request.flatMap((i) =>
    i.id_material_solicitado.toString()
  );
  let id_item = m_id.join(";");
  // Producto con problemas
  const sku_problem = aux_visit.visit?.point_of_sales?.some(
    (p) => p.visitas?.product_info?.beer_damage
  );

  // Preparando objeto de visita de venta
  let sales_visit = {
    ...aux_visit.visit,
    survey: aux_visit?.visit?.supervisor_survey,
    general_observation: aux_visit?.visit?.additional_observations?.comment,
    m_present: id_item.toString(),
    orden_taken: aux_visit?.visit.order_request !== null ? true : false,
    sku_problems: sku_problem,
    tag: aux_visit?.visit?.additional_observations?.visit_tag,
  };
 console.log("VISITA DE VENTA SI HAY PUNTO ", sales_visit );
 aux_visit.id = await insertSalesDraughtVisit.trigger({
    additionalScope: {
      visit: sales_visit,
    },
    onSuccess: function (data) {
      console.log("VISITA DE VENTA: " + data.id[0]);
      aux_visit.processed = "yes";
      aux_visit.idVisit = data.id[0];
      aux_visit.error = null;
      updateVisitLocal.trigger({
        additionalScope: {
          visit: aux_visit,
        },
      });
    },
    onFailure: function (error) {
      aux_visit.error = error;
      updateVisitLocal.trigger({
        additionalScope: {
          visit: aux_visit,
        },
      });
    },
  });
}
// Insertar visita del punto
if (aux_visit.visit) {
  if (aux_visit?.processed === "yes") {
    if (aux_visit.visit?.visit_type === "v") {
      if (
        Array.isArray(aux_visit.visit.point_of_sales) &&
        aux_visit.visit.point_of_sales.length
      ) {
        aux_visit.visit?.point_of_sales?.forEach((point) => {
          let point_visit = {
            ...point.visitas,
            general_visit_id: aux_visit?.idVisit,
            point_id: point?.id,
            id_client: aux_visit?.visit?.id_cliente_visitado,
            id_user: aux_visit.visit?.id_realizada_por,
          };
          console.log("VISITA DE PUNTO ", point_visit);
           insertPointSalesVisit.trigger({
            additionalScope: {
              visit_point: point_visit,
            },
            onSuccess: function (data) {
              console.log("VISITA EN EL PUNTO"  + data.id[0]);
              aux_visit.pointId = data.id[0],
              aux_visit.processed = "yes";
              aux_visit.error = null;
              updateVisitLocal.trigger({
                additionalScope: {
                  visit: aux_visit,
                },
              });
            },
            onFailure: function (error) {
              aux_visit.error = error;
              updateVisitLocal.trigger({
                additionalScope: {
                  visit: aux_visit,
                },
              });
            },
          });
        });
      }
    }
  }
}
// -- Visita en el punto precios

if (aux_visit.visit) {
  if (aux_visit?.processed === "yes") {
    if (aux_visit.visit?.visit_type === "v") {
      if (
        Array.isArray(aux_visit.visit.point_of_sales) &&
        aux_visit.visit.point_of_sales.length
      ) {
        aux_visit.visit?.point_of_sales?.forEach((point) => {
          if (
            point.visitas?.product_info !== null &&
            point.visitas?.product_info?.product_precence !== null
          ) {
            // -- Listado de precios
            const priceList = Object.entries(
              point.visitas?.product_info?.product_precence.sku_price || {}
            )
              .filter(([_, value]) => value !== null && value !== "")
              .map(([key, value]) => ({ format: key, price: value }));
            let price = {};

            priceList.forEach((priceItem) => {
              price = {
                ...point.visitas,
                general_visit_id: aux_visit?.idVisit,
                point_id: point?.id,
                id_client: aux_visit?.visit?.id_cliente_visitado,
                id_user: aux_visit.visit?.id_realizada_por,
                sku_id: point.visitas?.product_info?.product_precence?.id_sku,
                format: priceItem.format,
                price: priceItem.price,
                coin: point.visitas?.product_info?.coin,
                created_at: point.visitas?.fecha_visita,
              };

              console.log("INcertando precios ", point_visit);
              insertPointPrice.trigger({
                additionalScope: {
                  point_visit: price,
                },
                onSuccess: function (data) {
                  console.log("PRECIO : " + data.id[0]);
                  aux_visit.processed = "yes";
                  aux_visit.error = null;
                  updateVisitLocal.trigger({
                    additionalScope: {
                      visit: aux_visit,
                    },
                  });
                },
                onFailure: function (error) {
                  aux_visit.error = error;
                  updateVisitLocal.trigger({
                    additionalScope: {
                      visit: aux_visit,
                    },
                  });
                },
              });
            });
          }
        });
      }
    }
  }
}

// -- Insertar rotación  draugth
if (aux_visit.visit) {
  if (aux_visit?.processed === "yes") {
    if (aux_visit.visit?.visit_type === "v") {
      if (
        Array.isArray(aux_visit.visit.point_of_sales) &&
        aux_visit.visit.point_of_sales.length
      ) {
        aux_visit.visit?.point_of_sales?.forEach((point) => {
          // Si el punto NO tiene visitas → saltar este punto
          if (!point.visitas || point.visitas !== null) return;

          // Si visitas existe pero product_info no → saltar este punto
          if (!point.visitas.product_info || point.visitas.product_info !== null) return;

          // -- Rotación
          const rotation = {
            ...point.visitas,
            general_visit_id: aux_visit?.idVisit,
            point_id: point?.id,
            id_client: aux_visit?.visit?.id_cliente_visitado,
            id_user: aux_visit.visit?.id_realizada_por,
            sku_id: point.visitas?.product_info?.id_sku,
            average: point.visitas?.product_info?.point_ave?.ave_ccsa,
            full_keg: point.visitas?.product_info?.point_stock?.full_keg,
            empty_keg: point.visitas?.product_info?.point_stock?.empty_kegs,
            created_at: point.visitas?.fecha_visita,
          };

           insertPointRotation.trigger({
            additionalScope: { point_visit: rotation },
            onSuccess: function (data) {
              console.log("ROTACION: " + data.id[0]);
              aux_visit.processed = "yes";
              aux_visit.error = null;
              updateVisitLocal.trigger({
                additionalScope: { visit: aux_visit },
              });
            },
            onFailure: function (error) {
              aux_visit.error = error;
              updateVisitLocal.trigger({
                additionalScope: { visit: aux_visit },
              });
            },
          });
        });
      }
    }
  }
}
// -- Insertando competencia
if (aux_visit?.visit) {
  // Helpers
  const comp_prosp = aux_visit.visit?.visit_type === "p"; // Prospección
  const comp_hotel = aux_visit.visit?.id_channel === 2; // Canal Hotel

  // Fuente de datos de competencia
  let comp_source = [];

  if (comp_prosp && Array.isArray(aux_visit.visit?.competition_list)) {
    // Prospección
    comp_source = aux_visit.visit.competition_list;
  } else if (Array.isArray(aux_visit.visit?.point_of_sales)) {
    // Venta (con flatten directo)
    comp_source = aux_visit.visit.point_of_sales.flatMap((point) =>
      point?.visitas?.competition_list &&
      Array.isArray(point?.visitas?.competition_list)
        ? point.visitas.competition_list
        : []
    );
  }

  // Si no hay datos válidos, salimos
  if (!Array.isArray(comp_source) || comp_source.length === 0) return;
  // Iteración de competencia
  comp_source.forEach((comp) => {
    const isTonel =
      comp?.format === "tonel" &&
      comp?.stock_data_t &&
      comp?.stock_data_t !== null;
    const isBottleOrCan = comp?.format === "botella" || comp?.format === "lata";

    // COMPETENCIA TONEL (DRAUGHT)
    if (isTonel) {
      const priceEntries =
        comp?.stock_data_t?.price && comp?.stock_data_t?.price !== null
          ? Object.entries(comp?.stock_data_t?.price).filter(
              ([_, value]) => value !== null
            )
          : [];

      const entriesToIterate =
        priceEntries.length > 0 ? priceEntries : [["none", null]];

      entriesToIterate.forEach(([format, price]) => {
        const competition = {
          ...comp.visitas,
          general_visit_id: aux_visit.idVisit,
          id_client: aux_visit.visit.id_cliente_visitado,
          id_user: aux_visit.visit?.id_realizada_por,
          comp_id: comp.id_comp,
          ave: comp.stock_data_t.ave_comp,
          ...(priceEntries?.length > 0 ? { coin: comp.stock_data_t.coin } : {}),
          // Datos distintos según prospección o venta
          ...(!comp_prosp ? { point_id: comp.point_id } : {}),
          // Keg Floor (depende de tipo y canal)
          ...(comp_prosp
            ? {
                full_keg: aux_visit.visit?.keg_stock?.full_keg,
                empty_keg: aux_visit.visit?.keg_stock?.empty_keg,
                keg_floor:
                  Number(aux_visit.visit?.keg_stock?.full_keg) +
                  Number(aux_visit.visit?.keg_stock?.empty_keg),
              }
            : comp_hotel
            ? { keg_floor: comp.stock_data_t.keg_floor }
            : {}),
          ...(priceEntries?.length > 0 ? { format } : {}),
          ...(priceEntries?.length > 0 ? { price } : {}),
          created_at: aux_visit.visit.hora_inicio,
        };
        // -- Persisitiendo los datos de la competencia
        console.log("COMPETENCIA DATA ",competition);
         insertDraughtComp.trigger({
          additionalScope: { prop_comp: competition },

          onSuccess: function (data) {
            console.log("COMPETENCIA DRAUGHT: " + data.id[0]);
            aux_visit.processed = "yes";
            aux_visit.error = null;

            updateVisitLocal.trigger({
              additionalScope: { visit: aux_visit },
            });
          },

          onFailure: function (error) {
            aux_visit.error = error;
            updateVisitLocal.trigger({
              additionalScope: { visit: aux_visit },
            });
          },
        });
      });
    }
    // BOTELLA / LATA
    if (isBottleOrCan && aux_visit.processed === "yes") {
      const buttle_comp = {
        ...(comp?.stock_data_b || {}),
        id_visita: aux_visit.idVisit,
        id_usuario: aux_visit.visit?.id_realizada_por,
        comp_id: comp.id_comp,
        more_presence: comp.more_presence,
      };
      console.log("COMPETITION BUTTLE");
      insertVisitCompetenciaOnline.trigger({
        additionalScope: { competencia: buttle_comp },

        onSuccess: function (data) {
          const newId = data?.id?.[0]?.toString?.() || "";

          aux_visit.idCompetencia = aux_visit.idCompetencia
            ? `${aux_visit.idCompetencia} ${newId}`
            : newId;

          updateVisitLocal.trigger({
            additionalScope: { visit: aux_visit },
          });
        },
      });
    }
  });
}

// Guardando merchandising (Pendiente a probar)
if (aux_visit.visit.visit_type === "v") {
  if (aux_visit.visit.merchandising_request) {
    if (aux_visit?.processed === "yes") {
      aux_visit.visit.merchandising_request.forEach((merchandising) => {
        let auxMer = {
          ...merchandising,
          id_visita: aux_visit.idVisit,
          id_visita_ejecutoria: aux_visit.idVisit,
          id_cliente_visitado: aux_visit.visit.id_cliente_visitado,
        };
        console.log("Insertando Mechandising Creado", aux_visit.idVisit);
        if (auxMer.created) {
          console.log("Insertando Mechandising Creado", auxMer);
          insertMerchandising.trigger({
            additionalScope: {
              merchandising: auxMer,
            },
            onSuccess: function (data) {
              aux_visit.idMerchandising =
                aux_visit.idMerchandising != null
                  ? aux_visit.idMerchandising + " " + data.id[0].toString()
                  : data.id[0].toString();
              updateVisitLocal.trigger({
                additionalScope: {
                  visit: aux_visit,
                },
              });
            },
          });
        } else if (auxMer.updated) {
          console.log("MERCHANDISING", auxMer);
          executeMerchandising.trigger({
            additionalScope: {
              merchandising: auxMer,
            },
            onSuccess: function (data) {
              aux_visit.idMerchandising =
                aux_visit.idMerchandising != null
                  ? aux_visit.idMerchandising + " " + auxMer.id.toString()
                  : auxMer.id.toString();
              updateVisitLocal.trigger({
                additionalScope: {
                  visit: aux_visit,
                },
              });
            },
          });
        }
      });
    }
  }
}

//Pedido new way
if (aux_visit.visit.visit_type === "v") {
  if (aux_visit.visit.order_request) {
    if (aux_visit?.processed === "yes") {
      if (aux_visit.visit.order_request !== null) {
        const order = {
          ...aux_visit.visit.order_request,
          created_time: aux_visit?.visit?.order_request?.fecha_pedido,
          id_client: aux_visit?.visit?.id_cliente_visitado,
          id_created_by: aux_visit?.visit?.id_realizada_por,
          id_org: 1,
          id_visit: aux_visit?.idVisit,
          order_date: aux_visit?.visit?.order_request?.fecha_pedido,
          order_description:
            aux_visit?.visit?.order_request?.observaciones_pedido,
          order_do_date: aux_visit?.visit?.order_request?.fecha_comprometida,
        };
        insertOrderFromPending.trigger({
          additionalScope: {
            order: order,
          },
          onSuccess: function (data) {
            //Insert order lines
            aux_visit.idPedido =
              aux_visit.idPedido != null
                ? aux_visit.idPedido + " " + data.id_order[0].toString()
                : data.id_order[0].toString();
            if (aux_visit.visit.order_request.keg_amount !== null) {
              const line = {
                ...aux_visit.visit.order_request,
                cant_ordered: aux_visit?.visit?.order_request?.keg_amount,
                id_order: data.id_order[0],
                sku_id: 9,
              };
              console.log("ORDEN", line);
              insertOrderLineFromPending.trigger({
                additionalScope: {
                  orderLine: line,
                },
              });
            }
            updateVisitLocal.trigger({
              additionalScope: {
                visit: aux_visit,
              },
            });
          },
          onFailure: function (error) {
            aux_visit.error = error;
            updateVisitLocal.trigger({
              additionalScope: {
                visit: aux_visit,
              },
            });
          },
        });
      }
    }
  }
}
//Guardar visita de limpieza
if (aux_visit.visit) {
  if (aux_visit?.processed === "yes") {
    if (aux_visit.visit?.visit_type === "h") {
      if (
        Array.isArray(aux_visit.visit.point_of_sales) &&
        aux_visit.visit.point_of_sales.length
      ) {
        aux_visit.visit?.point_of_sales?.forEach((point) => {
          let cleaning_visit = {
            ...point.visitas,
            visit_id: aux_visit?.idVisit,
            point_id: point?.id,
            client_id: aux_visit?.visit?.id_cliente_visitado,
            user_id: aux_visit.visit?.id_realizada_por,
            technical_survey: point.visitas?.technical_cleaning_sourvy,
            additional_observations: point.visitas?.additional_observations,
            issue_report: point.visitas?.issue_report,
            machine_problem: !point.visitas?.issue_report?.fixid,
            comment_visit:
              point.visitas?.additional_observations.visit_tag_text,
          };

          console.log("Insertando visita en el punto ", cleaning_visit);
          aux_visit.id = insertSanitizerVisit.trigger({
            additionalScope: {
              visit: cleaning_visit,
            },
            onSuccess: function (data) {
              console.log("Visita registrada: " + data.id[0]);
              aux_visit.processed = "yes";
              aux_visit.error = null;
              aux_visit.cleaning_visit_id =
                aux_visit.cleaning_visit_id != null
                  ? aux_visit.cleaning_visit_id + " " + data.id[0].toString()
                  : data.id[0].toString();
              updateVisitLocal.trigger({
                additionalScope: {
                  visit: aux_visit,
                },
              });
            },
            onFailure: function (error) {
              aux_visit.error = error;
              updateVisitLocal.trigger({
                additionalScope: {
                  visit: aux_visit,
                },
              });
            },
          });
        });
      }
    }
  }
}
// -- Insertar Producto Dañado
if (aux_visit.visit) {
  if (aux_visit?.processed === "yes") {
    if (aux_visit.visit?.visit_type === "v") {
      if (
        aux_visit.visit.point_of_sales &&
        Array.isArray(aux_visit.visit.point_of_sales)
      ) {
        let damage_lote = {};
        aux_visit.visit?.point_of_sales?.forEach((point) => {
          // -- Producto dañado
          if (
            point.visitas?.beer_status &&
            Array.isArray(point.visitas.beer_status)
          ) {
            point.visitas?.beer_status.forEach((damage_b) => {
              damage_lote = {
                ...point.visitas,
                general_visit_id: aux_visit?.idVisit,
                point_id: point?.id,
                id_user: aux_visit.visit?.id_realizada_por,
                sku_id: damage_b.sku_id,
                damage_lote: damage_b.damage_lote,
                amount_of_product: damage_b.amount_of_damage_lote,
                problem: damage_b.beer_status,
                description: damage_b.observation_product_status,
              };
                 insertDamageLote.trigger({
                additionalScope: {
                  damage_lote: damage_lote,
                },
                onSuccess: function (data) {
                  console.log("CERVEZA DAÑADA: " + data.id[0]);
                  aux_visit.processed = "yes";
                  aux_visit.error = null;
                  updateVisitLocal.trigger({
                    additionalScope: {
                      visit: aux_visit,
                    },
                  });
                },
                onFailure: function (error) {
                  aux_visit.error = error;
                  updateVisitLocal.trigger({
                    additionalScope: {
                      visit: aux_visit,
                    },
                  });
                },
              });
            });
          }
        });
      }
    }
  }
}
// -- Insertando Rotacion General
if (
  aux_visit?.visit &&
  aux_visit.processed === "yes" &&
  aux_visit.visit.visit_type === "v"
) {
  const pos = aux_visit.visit.point_of_sales;
  if (!Array.isArray(pos) || pos.length === 0) return;

  // Helper para evitar repetición de map/filter
  const getValues = (fn) =>
    pos
      .map(fn)
      .filter((v) => v != null)
      .map(Number);

  // Datos base
  const store_keg = Number(aux_visit.visit?.supervisor_survey?.store_keg ?? 0);
  let rotation_general = {};
  const empty_keg = getValues(
    (p) => p.visitas?.product_info?.point_stock?.empty_kegs
  );
  const full_keg = getValues(
    (p) => p.visitas?.product_info?.point_stock?.full_keg
  );
  const ave_array = getValues(
    (p) => p.visitas?.product_info?.point_ave?.ave_ccsa
  );

  // Cálculos
  const sum_e_k = empty_keg.reduce((a, b) => a + b, 0);
  const sum_f_k = full_keg.reduce((a, b) => a + b, 0);
  const ave_sum = ave_array.reduce((a, b) => a + b, 0);

  // Resultados globales por cliente
  const client_ave = ave_array.length ? ave_sum / ave_array.length : 0;
  const total_keg = store_keg + sum_e_k + sum_f_k;

  // Procesar cada punto
  pos.forEach((point) => {
    if (point.visitas && point.visitas?.product_info) {
      rotation_general = {
        ...point.visitas,
        general_visit_id: aux_visit.idVisit,
        client_id: aux_visit.visit?.id_cliente_visitado,
        id_user: aux_visit.visit?.id_realizada_por,
        sku_id: point.visitas?.product_info?.id_sku,
        client_total_ave: client_ave,
        full_keg: sum_f_k,
        created_at: aux_visit.visit?.hora_inicio,
        total_keg: total_keg,
        empty_keg: sum_e_k,
      };
    }
     insertGeneralRotation.trigger({
      additionalScope: { rotation: rotation_general },
      onSuccess: (data) => {
        console.log("ROTACION:", data.id[0]);

        aux_visit.processed = "yes";
        aux_visit.error = null;

        updateVisitLocal.trigger({
          additionalScope: { visit: aux_visit },
        });
      },
      onFailure: (error) => {
        aux_visit.error = error;

        updateVisitLocal.trigger({
          additionalScope: { visit: aux_visit },
        });
      },
    });
  });
}
// -- Datos del cilindro co2
if (aux_visit.visit ) {
  if (aux_visit?.processed === "yes") {
    if (aux_visit.visit?.visit_type === "v") {
      if (
        Array.isArray(aux_visit.visit.point_of_sales) &&
        aux_visit.visit.point_of_sales.length
      ) {
        let co_2 = {};
        aux_visit.visit?.point_of_sales?.forEach((point) => {
          // -- Rotación
          if (point.technical_survey !== null) {
            co_2 = {
              ...point.visitas,
              general_visit_id: aux_visit?.idVisit,
              point_id: point?.id,
              id_client: aux_visit?.visit?.id_cliente_visitado,
              id_user: aux_visit.visit?.id_realizada_por,
              serial_number: point.visitas?.technical_survey?.serial_num,
              pressure: point.visitas?.technical_survey?.co2_pressure,
              sku_id: point.visitas?.technical_survey?.co2_id,
              additional_observation:
                point.visitas?.technical_survey?.tec_observation,
              created_at: point.visitas?.hora_inicio,
            };
          } else {
            return;
          }
        });
        console.log("CO2 ",co_2 );
         insertCO2Data.trigger({
          additionalScope: {
            tech_data: co_2,
          },
          onSuccess: function (data) {
            console.log("CILINDRO " + data.id[0]);
            aux_visit.processed = "yes";
            aux_visit.error = null;
            updateVisitLocal.trigger({
              additionalScope: {
                visit: aux_visit,
              },
            });
          },
          onFailure: function (error) {
            aux_visit.error = error;
            updateVisitLocal.trigger({
              additionalScope: {
                visit: aux_visit,
              },
            });
          },
        });
      }
    }
  }
}
// ✅ Insertando Keg price
if (!aux_visit?.visit) return;
if (aux_visit?.processed !== "yes") return;
if (aux_visit.visit?.visit_type !== "v") return;

const points = aux_visit.visit?.point_of_sales;

if (!Array.isArray(points) || points.length === 0) return;

let price = null;

points.forEach((point) => {
  const visitas = point?.visitas;
  const product = visitas?.product_info;

  // ⛔ Si no tiene visitas o product info, saltamos este punto
  if (!visitas || !product) return;

  const topFormat = product?.top_format;
  if (!topFormat) return;

  // ✅ Construir precio válido
  price = {
    ...visitas,
    general_visit_id: aux_visit?.idVisit,
    id_user: aux_visit.visit?.id_realizada_por,
    sku_id: product?.id_sku,
    keg_price: topFormat?.price_in_keg,
    coin: product?.coin,
    created_at: visitas?.hora_inicio,
  };

  console.log("KEG PRICE: ", price);
});

// ✅ Si no se encontró ningún precio, no insertes
if (!price) {
  console.log("No se encontró un precio válido para insertar");
  return;
}

console.log("TOP PRICE FINAL: ", price);

// ✅ Insertar precio
insertKegTopFormatPrice.trigger({
  additionalScope: { price },
  onSuccess: function (data) {
    console.log("KEG_PRICE " + data.id[0]);

    aux_visit.processed = "yes";
    aux_visit.error = null;

    updateVisitLocal.trigger({
      additionalScope: { visit: aux_visit },
    });
  },
  onFailure: function (error) {
    aux_visit.error = error;

    updateVisitLocal.trigger({
      additionalScope: { visit: aux_visit },
    });
  },
});


/////////////////////////////////////////////////////////////////////////////////////
