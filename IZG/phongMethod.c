#include <student/emptyMethod.h>
#include <student/application.h>
#include <student/cpu.h>
#include <student/globals.h>
#include <student/bunny.h>

/// \addtogroup shader_side Úkoly v shaderech
/// @{

/**
 * @brief This function represents vertex shader of phong method.
 *
 * @param data vertex shader data
 */
void phong_VS(GPUVertexShaderData*const data){
  /// \todo Naimplementujte vertex shader, který transformuje vstupní vrcholy do
  /// clip-space.<br>
  /// <b>Vstupy:</b><br>
  /// Vstupní vrchol by měl v nultém atributu obsahovat pozici vrcholu ve
  /// world-space (vec3) a v prvním
  /// atributu obsahovat normálu vrcholu ve world-space (vec3).<br>
  /// <b>Výstupy:</b><br>
  /// Výstupní vrchol by měl v nultém atributu obsahovat pozici vrcholu (vec3)
  /// ve world-space a v prvním
  /// atributu obsahovat normálu vrcholu ve world-space (vec3).
  /// Výstupní vrchol obsahuje pozici a normálu vrcholu proto, že chceme počítat
  /// osvětlení ve world-space ve fragment shaderu.<br>
  /// <b>Uniformy:</b><br>
  /// Vertex shader by měl pro transformaci využít uniformní proměnné obsahující
  /// view a projekční matici.
  /// View matici čtěte z nulté uniformní proměnné a projekční matici
  /// čtěte z první uniformní proměnné.
  /// <br>
  /// Využijte vektorové a maticové funkce.
  /// Nepředávajte si data do shaderu pomocí globálních proměnných.
  /// Vrchol v clip-space by měl být zapsán do proměnné gl_Position ve výstupní
  /// struktuře.
  (void)data;
  
  Vec3*pos = (Vec3*)data->inVertex.attributes[0].data;
  Vec3*col = (Vec3*)data->inVertex.attributes[1].data;
  Mat4*viewMatrix		= (Mat4*)data->uniforms->uniform[0].data;
  Mat4*projectionMatrix = (Mat4*)data->uniforms->uniform[1].data;
  Mat4 mvp;
  Vec4 vPos;

  multiply_Mat4_Mat4(&mvp, projectionMatrix, viewMatrix);
  copy_Vec3Float_To_Vec4(&vPos, pos, (float)1.0);
  multiply_Mat4_Vec4(&data->outVertex.gl_Position, &mvp, &vPos);
  copy_Vec3((Vec3*)data->outVertex.attributes[0].data, pos);
  copy_Vec3((Vec3*)data->outVertex.attributes[1].data, col);

}

/**
 * @brief This function represents fragment shader of phong method.
 *
 * @param data fragment shader data
 */
void phong_FS(GPUFragmentShaderData*const data){
  /// \todo Naimplementujte fragment shader, který počítá phongův osvětlovací
  /// model s phongovým stínováním.<br>
  /// <b>Vstup:</b><br>
  /// Vstupní fragment by měl v nultém fragment atributu obsahovat
  /// interpolovanou pozici ve world-space a v prvním
  /// fragment atributu obsahovat interpolovanou normálu ve world-space.<br>
  /// <b>Výstup:</b><br>
  /// Barvu zapište do proměnné gl_FragColor ve výstupní struktuře.<br>
  /// <b>Uniformy:</b><br>
  /// Pozici kamery přečtěte z uniformní 3 a pozici
  /// světla přečtěte z uniformní 2.
  /// <br>
  /// Dejte si pozor na velikost normálového vektoru, při lineární interpolaci v
  /// rasterizaci může dojít ke zkrácení.
  /// Zapište barvu do proměnné color ve výstupní struktuře.
  /// Shininess faktor nastavte na 40.f
  /// Difuzní barvu materiálu nastavte podle normály povrchu.
  /// V případě, že normála směřuje kolmo vzhůru je difuzní barva čistě bílá.
  /// V případě, že normála směřuje vodorovně nebo dolů je difuzní barva čiště zelená.
  /// Difuzní barvu spočtěte lineární interpolací zelené a bíle barvy pomocí interpolačního parameteru t.
  /// Interpolační parameter t spočtěte z y komponenty normály pomocí t = y*y (samozřejmě s ohledem na negativní čísla).
  /// Spekulární barvu materiálu nastavte na čistou bílou.
  /// Barvu světla nastavte na bílou.
  /// Nepoužívejte ambientní světlo.<br>
	Vec3*attr_1; Vec3*attr_2;
	Vec3*cam_p;
	Vec3*light_p;
	Vec3 light;
	Vec3 cam;
	Vec3 reflection;
	float temp;
	float reflec;

	attr_1 = (Vec3*)data->inFragment.attributes[0].data;
	attr_2 = (Vec3*)data->inFragment.attributes[1].data;
	light_p = (Vec3*)data->uniforms->uniform[2].data;
	cam_p = (Vec3*)data->uniforms->uniform[3].data;

	normalize_Vec3(attr_2, attr_2);

	sub_Vec3(&light, light_p, attr_1);
	normalize_Vec3(&light, &light);

	sub_Vec3(&cam, cam_p, attr_1);
	normalize_Vec3(&cam, &cam);
	reflect(&reflection, &light, attr_2);

	temp = (float)fmax((float)0.0, dot_Vec3(&reflection, &cam));
	reflec = (float)pow(temp, 40.f);
	temp = (float)(reflec + fmax(dot_Vec3(&light, attr_2), (float)0.0));
	init_Vec4(&data->outFragment.gl_FragColor, reflec, temp, reflec, (float)1.0);


}

/// @}

/// \addtogroup cpu_side Úkoly v cpu části
/// @{

/**
 * @brief This struct holds all variables of phong method.
 */
struct PhongData{
/// \todo Zde si vytvořte proměnné, které budete potřebovat (id bufferů, programu, ...)
	ProgramID program;
	GPU gpu;
	VertexPullerID puller;
	BufferID indices;
	BufferID vertices;
	Vec3 light;
	Vec4 color;
}phongData;///< this variable holds all data for phong method

/**
 * @brief This function initializes phong method.
 *
 * @param a data for initialization
 */
void phong_onInit(void*a){
  GPU*gpu = (GPU*)a;
  Vec4 clear;
  init_Vec4(&clear,.1f,.1f,.1f,1.f);
  cpu_clearColor(gpu,clear);
  cpu_clearDepth(gpu,1.f);

/// \todo Doprogramujte inicializační funkci.
/// Zde byste měli vytvořit buffery na GPU, nahrát data do bufferů, vytvořit
/// vertex puller a správně jej nakonfigurovat, vytvořit program, připojit k
/// němu shadery a nastavit atributy, které se posílají mezi vs a fs.
/// Do bufferů nahrajte vrcholy králička (pozice, normály) a indexy na vrcholy
/// ze souboru bunny.h.
/// Shader program by měl odkazovat na funkce/shadery phong_VS a phong_FS.
/// V konfiguraci vertex pulleru nastavte dvě čtecí hlavy.
/// Jednu pro pozice vrcholů a druhou pro normály vrcholů.
/// Nultý vertex/fragment atribut by měl obsahovat pozici vertexu.
/// První vertex/fragment atribut by měl obsahovat normálu vertexu.
/// Nastavte, které atributy (jaký typ) se posílají z vertex shaderu do fragment shaderu.
/// <b>Seznam funkcí, které jistě využijete:</b>
///  - cpu_createBuffer()
///  - cpu_bufferData()
///  - cpu_createVertexPuller()
///  - cpu_setVertexPuller()
///  - cpu_enableVertexPullerHead()
///  - cpu_setVertexPullerIndexing()
///  - cpu_createProgram()
///  - cpu_attachShaders()
///  - cpu_setVS2FSType()

  phongData.indices = cpu_createBuffer(gpu);
  phongData.vertices = cpu_createBuffer(gpu);

  cpu_bufferData(gpu, phongData.vertices, sizeof(bunnyVertices), (struct BunnyVertex *)bunnyVertices);
  cpu_bufferData(gpu, phongData.indices, sizeof(bunnyIndices), (uint32_t *)bunnyIndices);
  
  phongData.puller = cpu_createVertexPuller(gpu);

  cpu_setVertexPuller(gpu, phongData.puller, 0, ATTRIBUTE_VEC3, sizeof(struct BunnyVertex), 0, phongData.vertices);
  cpu_enableVertexPullerHead(gpu, phongData.puller, 0);

  cpu_setVertexPuller(gpu, phongData.puller, 1, ATTRIBUTE_VEC3, sizeof(struct BunnyVertex), sizeof(Vec3), phongData.vertices);
  cpu_enableVertexPullerHead(gpu, phongData.puller, 1);
  
  cpu_setVertexPullerIndexing(gpu, phongData.puller, UINT32, phongData.indices);

  phongData.program = cpu_createProgram(gpu);
  
  cpu_attachShaders(gpu, phongData.program, phong_VS, phong_FS);
  cpu_setVS2FSType(gpu, phongData.program, 0, ATTRIBUTE_VEC3);

}

/**
 * @brief This function draws phong method.
 *
 * @param a data
 */
void phong_onDraw(void*a){
  GPU*gpu = (GPU*)a;
  cpu_clear(gpu);

/// \todo Doprogramujte kreslící funkci.
/// Zde byste měli aktivovat shader program, aktivovat vertex puller, nahrát
/// data do uniformních proměnných a
/// vykreslit trojúhelníky pomocí funkce cpu_drawTriangles.
/// Data pro uniformní proměnné naleznete v externích globálních proměnnénych
/// viewMatrix, projectionMatrix, cameraPosition a lightPosition
/// <b>Seznam funkcí, které jistě využijete:</b>
///  - cpu_useProgram()
///  - cpu_bindVertexPuller()
///  - cpu_uniformMatrix4f()
///  - cpu_uniform3f()
///  - cpu_drawTriangles()
///  - cpu_unbindVertexPuller
  int i = 0;
  cpu_useProgram(gpu, phongData.program);
  cpu_bindVertexPuller(gpu, phongData.puller);
  cpu_programUniformMatrix4f(gpu, phongData.program, i++, viewMatrix);
  cpu_programUniformMatrix4f(gpu, phongData.program, i++, projectionMatrix);
  cpu_programUniform3f(gpu, phongData.program, i--, cameraPosition);
  cpu_programUniform3f(gpu, phongData.program, i, lightPosition);
  uint32_t nof = sizeof(bunnyIndices) / sizeof(VertexIndex); 
  cpu_drawTriangles(gpu, nof);
  cpu_unbindVertexPuller(gpu);
}

/**
 * @brief This functions frees all the phong data.
 *
 * @param a data
 */
void phong_onExit(void*a){
  GPU*gpu = (GPU*)a;

  ///\todo Zde uvolněte alokované zdroje

  cpu_deleteBuffer(gpu, phongData.indices);
  cpu_deleteBuffer(gpu, phongData.vertices);
  cpu_deleteVertexPuller(gpu, phongData.puller);
  cpu_deleteProgram(gpu, phongData.program);

}

/// @}
