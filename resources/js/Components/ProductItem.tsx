import React from "react";
import { Product } from "@/types";
import { Link } from "@inertiajs/react";

const ProductItem = ({ product }: { product: Product }) => {
  return (
    <div className="card bg-base-100 shadow-xl">
      <Link href={route("product.show", { product: product.slug })}>
        <figure>
          <img
            src={product.image}
            alt={product.title}
            className="aspect-square object-cover"
          />
        </figure>
      </Link>
      <div className="card-body">
        <h2 className="card-title">{product.title}</h2>
        <p>
          by &nbsp;
          <Link href="/" className="hover:underline">
            {product.user.name}
          </Link>
          &nbsp; in &nbsp;
          <Link href="/" className="hover:underline">
            {product.department.name}
          </Link>
        </p>
        <div className="card-actions items-center justify-between mt-3">
          <button className="btn btn-primary"> Add to Cart</button>
          <span className="text-2xl">{product.price}</span>
        </div>
      </div>
    </div>
  );
};

export default ProductItem;
