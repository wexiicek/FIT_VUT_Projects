#include <student/gpu.h>

#include <assert.h>
#include <math.h>
#include <stdio.h>
#include <string.h>
#include <iostream>

uint32_t convert_to_32(uint64_t num) {
	return (uint32_t)num;
}

void vertex_attribute_copy(GPU const*const gpu, GPUVertexPullerHead const*const vertex_head, uint64_t vID, GPUAttribute*const gpu_attribute) {
	if (!vertex_head->enabled) {
		return;
	}

	GPUBuffer const*const buffer = gpu_getBuffer(gpu, vertex_head->bufferId);
	uint8_t const*ptr = (uint8_t*)buffer->data;
	
	uint32_t const head_size = convert_to_32(vertex_head->type);
	uint32_t const head_stride = convert_to_32(vertex_head->stride);
	uint32_t const head_offset = convert_to_32(vertex_head->offset);

	memcpy(gpu_attribute->data, ptr + head_offset + head_stride *vID, head_size);
}

Vec4 vec4_comp(Vec4 const&coord, uint32_t width, uint32_t height) {
	Vec4 copy;
	int i = 0;
	copy.data[i++] = width * (coord.data[i] / (float)2.0 + (float)0.5);
	copy.data[i++] = height * (coord.data[i] / (float)2.0 + (float)0.5);
	copy.data[i++] = coord.data[i];
	copy.data[i++] = coord.data[i];
	return copy;
}

void vp_copy(GPU const*const gpu, GPUVertexPuller const*const puller, GPUInVertex*const in_vertex,  uint32_t v) {
	uint32_t vertexId = v;
	in_vertex->gl_VertexID = vertexId;

	if (gpu_isBuffer(gpu, puller->indices.bufferId)) {
		const GPUBuffer *tmp = gpu_getBuffer(gpu, puller->indices.bufferId);
		switch (puller->indices.type) {
		case UINT8:
			in_vertex->gl_VertexID = ((uint8_t*)tmp->data)[vertexId];
			break;
		case UINT16:
			in_vertex->gl_VertexID = ((uint16_t*)tmp->data)[vertexId];
			break;
		case UINT32:
			in_vertex->gl_VertexID = ((uint32_t*)tmp->data)[vertexId];
			break;
		}
	}

	for (int i = 0; i < 8; i++) {
		vertex_attribute_copy(gpu, puller->heads + i, in_vertex->gl_VertexID, in_vertex->attributes + i);
	}

}

void pd_copy(Vec4*const a, Vec4 const*const b) {
	int pos = 3;
	for (int i = 1; i < 4; i++){
		a->data[i] = b->data[i] / b->data[pos];
	}
	a->data[pos] = b->data[pos];
}




/// \addtogroup gpu_side Implementace vykreslovacího řetězce - vykreslování trojúhelníků
/// @{

/**
 * @brief This function should draw triangles
 *
 * @param gpu gpu
 * @param nofVertices number of vertices
 */
void gpu_drawTriangles(GPU *const gpu, uint32_t nofVertices)
{

	/// \todo Naimplementujte vykreslování trojúhelníků.
	/// nofVertices - počet vrcholů
	/// gpu - data na grafické kartě
	/// Vašim úkolem je naimplementovat chování grafické karty.
	/// Úkol je složen:
	/// 1. z implementace Vertex Pulleru
	/// 2. zavolání vertex shaderu pro každý vrchol
	/// 3. rasterizace
	/// 4. zavolání fragment shaderu pro každý fragment
	/// 5. zavolání per fragment operací nad fragmenty (depth test, zápis barvy a hloubky)
	/// Více v připojeném videu.
	(void)gpu;
	  (void)nofVertices;

	  GPUProgram      const* program = gpu_getActiveProgram(gpu);
	  GPUVertexPuller const* puller = gpu_getActivePuller(gpu);

	  GPUVertexShaderData   vs_data_1;
	  GPUVertexShaderData   vs_data_2;
	  GPUVertexShaderData   vs_data_3;
	  GPUFragmentShaderData fs_data;

	  Vec4 pos_1;
	  Vec4 pos_2;
	  Vec4 pos_3;

	  Vec4 ndc_1;
	  Vec4 ndc_2;
	  Vec4 ndc_3;

	  vs_data_1.uniforms = &program->uniforms;
	  vs_data_2.uniforms = &program->uniforms;
	  vs_data_3.uniforms = &program->uniforms;

	  fs_data.uniforms = &program->uniforms;

	  for (uint32_t i = 0; i < nofVertices;i += 3) {
		  vp_copy(gpu, puller, &vs_data_1.inVertex, i);
		  vp_copy(gpu, puller, &vs_data_2.inVertex, i + 1);
		  vp_copy(gpu, puller, &vs_data_3.inVertex, i + 2);

		  program->vertexShader(&vs_data_1);
		  program->vertexShader(&vs_data_2);
		  program->vertexShader(&vs_data_3);

		  copy_Vec4(&pos_1, &vs_data_1.outVertex.gl_Position);
		  copy_Vec4(&pos_2, &vs_data_2.outVertex.gl_Position);
		  copy_Vec4(&pos_3, &vs_data_3.outVertex.gl_Position);

		  pd_copy(&ndc_1, &pos_1);
		  pd_copy(&ndc_2, &pos_2);
		  pd_copy(&ndc_3, &pos_3);

		  program->fragmentShader(&fs_data);

	  }
}

/// @}
