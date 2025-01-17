import React, { useEffect, useState } from "react";
import { zodResolver } from "@hookform/resolvers/zod";
import { useForm, Controller } from "react-hook-form";
import { z } from "zod";
import { Label } from "@/components/ui/label";
import { Input } from "@/components/ui/input";
import { Loader2 } from "lucide-react";

import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import axios from "axios";
import { Button } from "@/components/ui/button";
import { useNavigate, useParams } from "react-router-dom";
import { useMutation, useQuery, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";

const formSchema = z.object({
  name: z.string().min(2, "Name must be at least 2 characters"),
});

const Update = () => {
  const [isLoading, setIsLoading] = useState(false);
  const queryClient = useQueryClient();
  const { id } = useParams();
  const user = JSON.parse(localStorage.getItem("user"));
  const token = user.token;
  const navigate = useNavigate();

  const defaultValues = {
    name: "",
  };

  const {
    control,
    handleSubmit,
    formState: { errors },
    setValue,
    setError,
  } = useForm({ resolver: zodResolver(formSchema), defaultValues });

  const {
    data: editRole,
    isLoading: isEditRoleDataLoading,
    isError: isEditRoleDataError,
  } = useQuery({
    queryKey: ["editRole", id], // This is the query key
    queryFn: async () => {
      try {
        const response = await axios.get(`/api/roles/${id}`, {
          headers: {
            "Content-Type": "application/json",
            Authorization: `Bearer ${token}`,
          },
        });
        return response.data?.data; // Return the fetched data
      } catch (error) {
        throw new Error(error.message);
      }
    },
    keepPreviousData: true, // Keep previous data until the new data is available
  });

  useEffect(() => {
    if (editRole) {
      setValue("name", editRole.Role?.name);
    }
  }, [editRole, setValue]);
  const { Permissions, RolePermissions } = editRole || {};
  const updateMutation = useMutation({
    mutationFn: async (data) => {
      const response = await axios.put(`/api/roles/${id}`, data, {
        headers: {
          "Content-Type": "application/json",
          Authorization: `Bearer ${token}`, // Include the Bearer token
        },
      });
      return response.data;
    },
    onSuccess: (data) => {
      queryClient.invalidateQueries("roles");

      toast.success("Role Updated Successfully");
      setIsLoading(false);
      navigate("/roles");
    },
    onError: (error) => {
      setIsLoading(false);
      if (error.response && error.response.data.errors) {
        const serverStatus = error.response.data.status;
        const serverErrors = error.response.data.errors;
        if (serverStatus === false) {
          if (serverErrors.name) {
            setError("name", {
              type: "manual",
              message: serverErrors.name[0], // The error message from the server
            });
            // toast.error("The poo has already been taken.");
          }
        } else {
          toast.error("Failed to update Role details.");
        }
      } else {
        toast.error("Failed to update Role details.");
      }
      console.log("got error ", error);
    },
  });
  const onSubmit = (data) => {
    console.log("Clicked");
    setIsLoading(true);
    updateMutation.mutate(data);
  };

  return (
    <>
      <div className="p-5">
        {/* breadcrumb start */}
        <div className=" mb-7 text-sm">
          <div className="flex items-center space-x-2 text-gray-700">
            <span className="">
              {/* Users */}
              <Button
                onClick={() => navigate("/roles")}
                className="p-0 text-blue-700 text-sm font-light"
                variant="link"
              >
                Roles
              </Button>
            </span>
            <span className="text-gray-400">/</span>
            <span className="dark:text-gray-500">Edit</span>
          </div>
        </div>
        {/* breadcrumb ends */}

        {/* form style strat */}
        <div className="px-5 pb-7 pt-1 w-full dark:bg-background bg-white shadow-lg border  rounded-md">
          <div className="w-full py-3 flex justify-start items-center">
            <h2 className="text-lg  font-normal">Update Role</h2>
          </div>
          {/* row starts */}
          <form onSubmit={handleSubmit(onSubmit)}>
            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-3 gap-7 md:gap-4">
              <div className="relative">
                <Label className="font-normal" htmlFor="name">
                  Name: <span className="text-red-500">*</span>
                </Label>
                <Controller
                  name="name"
                  control={control}
                  render={({ field }) => (
                    <Input
                      {...field}
                      id="name"
                      className="mt-1"
                      type="text"
                      placeholder="Enter name"
                    />
                  )}
                />
                {errors.name && (
                  <p className="absolute text-red-500 text-sm mt-1 left-0">
                    {errors.name.message}
                  </p>
                )}
              </div>
            </div>

            <div className="w-full mb-8 grid grid-cols-1 md:grid-cols-4 items-center gap-7 md:gap-4">
              {Permissions &&
                Permissions.map((permission) => (
                  <div
                    key={permission.id}
                    className="relative flex gap-2 md:pt-10 md:pl-2"
                  >
                    <Controller
                      name={permission.name}
                      control={control}
                      render={({ field }) => (
                        <input
                          {...field}
                          id={permission.name}
                          checked={
                            RolePermissions &&
                            RolePermissions.includes(permission.name)
                          } // Check if permission is in RolePermissions
                          onChange={(e) => {
                            field.onChange(e.target.checked ? 1 : 0); // Map true/false to 1/0
                          }}
                          type="checkbox"
                          className="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground"
                        />
                      )}
                    />
                    <Label className="font-normal" htmlFor={permission.name}>
                      {permission.name}
                    </Label>
                  </div>
                ))}
            </div>

            {/* row ends */}
            <div className="w-full gap-4 mt-4 flex justify-end items-center">
              <Button
                type="button"
                className=" shadow-xl dark:text-white bg-red-600 hover:bg-red-700"
                onClick={() => navigate("/roles")}
              >
                Cancel
              </Button>

              <Button
                type="submit"
                disabled={isLoading}
                className="shadow-xl dark:text-white bg-green-600 hover:bg-green-700"
              >
                {isLoading ? (
                  <>
                    <Loader2 className="animate-spin mr-2" /> {/* Spinner */}
                    Submitting...
                  </>
                ) : (
                  "Submit"
                )}
              </Button>
            </div>
          </form>
        </div>
      </div>
    </>
  );
};

export default Update;
